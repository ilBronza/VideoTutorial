<?php

namespace IlBronza\VideoTutorial\Tests\Feature;

use DOMDocument;
use DOMXPath;
use IlBronza\Menu\Helpers\MenuPayloadBuilder;
use IlBronza\VideoTutorial\Facades\VideoTutorial as VideoTutorialRoutes;
use IlBronza\VideoTutorial\Models\Videotutorial;
use IlBronza\VideoTutorial\Tests\Fixtures\ProfileMenu;
use IlBronza\VideoTutorial\Tests\TestCase;
use IlBronza\VideoTutorial\VideoTutorial as Package;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use RuntimeException;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;
use Spatie\Permission\Models\Role;

class ContextualVideoUploadTest extends TestCase
{
    private const PAGE_ROUTE = 'orders.show';

    private ?string $videoStorage = null;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('web')->get('example/orders/{order}', fn () => view('uikittemplate::app'))->name(self::PAGE_ROUTE);
        Route::middleware('web')->get('example/products', fn () => view('uikittemplate::app'))->name('products.index');
        Route::getRoutes()->refreshNameLookups();

        $providers = config('menu.serviceProviders');
        array_unshift($providers, ProfileMenu::class);
        config([
            'menu.serviceProviders' => $providers,
        ]);

        $this->videoStorage = sys_get_temp_dir() . '/videotutorial-' . Str::uuid();
        config([
            'media-library.disk_name' => 'test-video',
            'filesystems.disks.test-video' => [
                'driver' => 'local',
                'root' => $this->videoStorage,
                'url' => '/test-video',
            ],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->videoStorage !== null) {
            File::deleteDirectory($this->videoStorage);
        }

        parent::tearDown();
    }

    public function test_migration_creates_the_maker_role_for_the_web_guard(): void
    {
        $this->assertSame('videoTutrialMaker', Role::findByName(Package::MAKER_ROLE, 'web')->name);
    }

    public function test_maker_uploads_a_video_from_the_profile_menu_and_completes_its_draft(): void
    {
        $this->signIn(Package::MAKER_ROLE);
        $page = $this->get('/example/orders/42')->assertOk()->assertSee('href="#videotutorial-context-upload"', false);
        $upload = $this->uploadAction($page);

        $result = $this->post($upload, [
            'file' => $this->video(),
        ]);
        $result->assertRedirect()->assertSessionHasNoErrors();

        $draft = Videotutorial::query()->sole();
        $edit = VideoTutorialRoutes::route('contextualUpload.edit', [
            $draft,
        ]);
        $result->assertRedirect($edit);
        $this->assertSame('Order guide', $draft->title);
        $this->assertSame(0, $draft->sorting_index);
        $this->assertSame('42', $draft->context_uploader_id);
        $this->assertSame([
            self::PAGE_ROUTE,
        ], $draft->route_names);
        $this->assertFalse($draft->isVisibleInCatalog());

        $media = $draft->getFirstMedia('file');
        $this->assertSame('video/mp4', $media?->mime_type);
        $this->assertSame((string) $media->getKey(), $draft->file);
        Storage::disk('test-video')->assertExists($media->getPathRelativeToRoot());

        $this->get('/example/orders/75')->assertOk()->assertDontSee($media->getUrl());
        $this->get($edit)->assertOk()->assertSee('Edit video guide')->assertSee($draft->title)->assertSee('name="plain_description"', false);

        $update = VideoTutorialRoutes::route('contextualUpload.update', [
            $draft,
        ]);
        $this->post($update, [
            '_method' => 'PUT',
            'name' => 'Orders',
            'title' => 'How to view an order',
            'plain_short_description' => 'Open the order & check its status.',
            'plain_description' => 'Use < and > to switch orders.',
            'active' => '1',
            'show' => '1',
            'route_names' => [
                'another.page',
            ],
            'ib-editor' => true,
        ])->assertRedirect($edit);

        $draft->refresh();
        $this->assertSame('Use &lt; and &gt; to switch orders.', $draft->description);
        $this->assertSame([
            self::PAGE_ROUTE,
        ], $draft->route_names);
        $this->get('/example/orders/75')->assertOk()->assertSee($media->getUrl())->assertSee('How to view an order');
        $this->get('/example/products')->assertOk()->assertDontSee($media->getUrl());
    }

    public function test_progressive_profile_menu_uses_the_upload_form_on_the_current_page(): void
    {
        $this->signIn(Package::MAKER_ROLE);
        config([
            'menu.progressive.enabled' => true,
        ]);
        $this->uploadAction($this->get('/example/orders/42')->assertOk());

        $root = (new MenuPayloadBuilder(app('menu')))->root();
        $account = collect($root['navbars'])->pluck('items')->collapse()->firstWhere('name', 'account');

        $this->getJson($account['childrenUrl'])->assertOk()->assertJsonFragment([
            'name' => 'videotutorial.contextUpload',
            'toggle' => '#videotutorial-context-upload',
        ]);
    }

    public function test_reader_cannot_upload_and_maker_cannot_edit_another_authors_draft(): void
    {
        $this->signIn('reader');
        $this->get('/example/orders/42')->assertOk()->assertDontSee('videotutorial-context-upload');
        $this->postJson($this->signedUpload(42))->assertForbidden();

        $this->signIn(Package::MAKER_ROLE, 73);
        $this->postJson($this->signedUpload(42))->assertForbidden();
        $draft = $this->tutorial('Another author', [
            'context_uploader_id' => '42',
        ]);
        $this->get(VideoTutorialRoutes::route('contextualUpload.edit', [
            $draft,
        ]))->assertNotFound();
        $this->putJson(VideoTutorialRoutes::route('contextualUpload.update', [
            $draft,
        ]))->assertNotFound();
    }

    public function test_invalid_file_or_tampered_route_does_not_create_a_draft(): void
    {
        $this->signIn(Package::MAKER_ROLE);
        $upload = $this->signedUpload(42);

        $this->postJson(str_replace('orders.show', 'different.page', $upload))->assertForbidden();
        $this->postJson($upload, [
            'file' => UploadedFile::fake()->create('guide.txt'),
        ])->assertUnprocessable()->assertJsonValidationErrors('file');

        $this->assertSame(0, Videotutorial::count());
    }

    public function test_failed_upload_removes_the_draft_and_its_file(): void
    {
        $this->signIn(Package::MAKER_ROLE);
        Event::listen(MediaHasBeenAddedEvent::class, function (): void {
            throw new RuntimeException('Simulated failure after copying the video');
        });

        $this->postJson($this->signedUpload(42), [
            'file' => $this->video(),
        ])->assertServerError();

        $this->assertSame(0, Videotutorial::count());
        $this->assertDatabaseCount('media', 0);
        $this->assertSame([], Storage::disk('test-video')->allFiles());
    }

    private function signedUpload(int $maker): string
    {
        return URL::signedRoute(config('videotutorial.routePrefix') . 'contextualUpload.store', [
            'maker' => $maker,
            'page' => self::PAGE_ROUTE,
        ]);
    }

    private function uploadAction(TestResponse $response): string
    {
        $document = new DOMDocument();
        $document->loadHTML($response->getContent(), LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $action = (new DOMXPath($document))->evaluate('string(//*[@id="videotutorial-context-upload"]//form/@action)');
        $this->assertNotEmpty($action);

        return $action;
    }

    private function video(): UploadedFile
    {
        return UploadedFile::fake()
            ->createWithContent('Order guide.mp4', pack('Na4a4Na8', 24, 'ftyp', 'isom', 0, 'isommp42'))
            ->mimeType('video/mp4');
    }
}
