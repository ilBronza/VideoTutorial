<?php

namespace IlBronza\VideoTutorial\Tests;

use IlBronza\AccountManager\Models\User;
use IlBronza\Buttons\ButtonsServiceProvider;
use IlBronza\CRUD\CRUDServiceProvider;
use IlBronza\CRUD\Models\Media;
use IlBronza\Datatables\DatatablesServiceProvider;
use IlBronza\Form\FormServiceProvider;
use IlBronza\FormField\FormFieldServiceProvider;
use IlBronza\Menu\MenuServiceProvider;
use IlBronza\UikitTemplate\UikitTemplate;
use IlBronza\UikitTemplate\UikitTemplateServiceProvider;
use IlBronza\Ukn\UknServiceProvider;
use IlBronza\VideoTutorial\Models\Videotutorial;
use IlBronza\VideoTutorial\VideoTutorialServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use ReflectionClass;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ButtonsServiceProvider::class,
            CRUDServiceProvider::class,
            DatatablesServiceProvider::class,
            FormServiceProvider::class,
            FormFieldServiceProvider::class,
            MediaLibraryServiceProvider::class,
            MenuServiceProvider::class,
            PermissionServiceProvider::class,
            UikitTemplateServiceProvider::class,
            UknServiceProvider::class,
            VideoTutorialServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set([
            'app.key' => 'base64:' . base64_encode(random_bytes(32)),
            'app.template' => UikitTemplate::class,
            'activitylog.enabled' => false,
            'auth.providers.users.model' => User::class,
            'database.default' => 'testing',
            'database.connections.testing' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'foreign_key_constraints' => true,
            ],
            'logging.default' => 'null',
            'media-library.media_model' => Media::class,
            'media-library.max_file_size' => 10240,
            'media-library.image_generators' => [],
            'menu.childrenPerColumn' => 8,
            'menu.serviceProviders' => [],
            'session.driver' => 'array',
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
        });

        $permissions = dirname((new ReflectionClass(PermissionServiceProvider::class))->getFileName(), 2);
        (require $permissions . '/database/migrations/create_permission_tables.php.stub')->up();

        $mediaLibrary = dirname((new ReflectionClass(MediaLibraryServiceProvider::class))->getFileName(), 2);
        (require $mediaLibrary . '/database/migrations/create_media_table.php.stub')->up();

        $this->artisan('migrate')->assertSuccessful();
    }

    protected function signIn(string $role, int $id = 42): void
    {
        DB::table('users')->insertOrIgnore([
            'id' => $id,
        ]);

        $user = new User();
        $user->forceFill([
            'id' => $id,
            'name' => 'Test User',
        ]);
        $user->setRelation('roles', collect([
            new Role([
                'name' => $role,
                'guard_name' => 'web',
            ]),
        ]));

        $this->actingAs($user);
    }

    protected function tutorial(string $name, array $attributes = []): Videotutorial
    {
        return Videotutorial::forceCreate(array_replace([
            'name' => $name,
            'title' => $name,
            'active' => true,
            'show' => true,
        ], $attributes));
    }
}
