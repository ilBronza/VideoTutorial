<?php

namespace IlBronza\VideoTutorial\Http\Controllers\ContextualUpload;

use IlBronza\VideoTutorial\Facades\VideoTutorial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\DiskCannotBeAccessed;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Throwable;

class VideoTutorialUploadController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $maker = (string) $request->user()->getAuthIdentifier();
        abort_unless($request->query('maker') === $maker, 403);

        $page = $request->query('page');
        $route = is_string($page) ? Route::getRoutes()->getByName($page) : null;
        abort_unless($route && in_array('GET', $route->methods(), true), 404);

        $maximumKilobytes = intdiv((int) config('media-library.max_file_size'), 1024);
        $request->validateWithBag('contextualVideoUpload', [
            'file' => [
                'required',
                'file',
                'mimetypes:video/*',
                'max:' . $maximumKilobytes,
            ],
        ]);

        $file = $request->file('file');
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = Str::limit(trim(str_replace([
            '<',
            '>',
        ], '', $filename)), 255, '') ?: __('videotutorial::videotutorial.newVideo');

        $tutorial = app(config('videotutorial.models.videotutorial.class'));
        $tutorial->forceFill([
            'name' => $name,
            'title' => $name,
            'sorting_index' => 0,
            'active' => false,
            'show' => false,
            'context_uploader_id' => $maker,
            'route_names' => [
                $page,
            ],
        ]);

        $media = null;
        try {
            $tutorial->getConnection()->transaction(function () use ($tutorial, $file, $name, &$media): void {
                $tutorial->saveOrFail();

                try {
                    $media = $tutorial->addMedia($file)
                        ->usingName($name)
                        ->usingFileName($tutorial->getKey() . '.' . ($file->guessExtension() ?: 'bin'))
                        ->toMediaCollection('file');

                    $tutorial->file = (string) $media->getKey();
                    $tutorial->saveOrFail();
                } catch (Throwable $exception) {
                    // A conversion can fail after Spatie has written the original file.
                    $media ??= $tutorial->media()->first();

                    throw $exception;
                }
            });
        } catch (Throwable $exception) {
            if ($media) {
                try {
                    app(Filesystem::class)->removeAllFiles($media);
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }
            }

            if ($exception instanceof DiskCannotBeAccessed) {
                report($exception);

                throw ValidationException::withMessages([
                    'file' => __('videotutorial::videotutorial.uploadFailed'),
                ])->errorBag('contextualVideoUpload');
            }

            throw $exception;
        }

        return redirect()->to(VideoTutorial::route('contextualUpload.edit', [
            $tutorial,
        ]));
    }
}
