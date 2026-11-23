<?php

use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialIndexController;
use IlBronza\VideoTutorial\Http\Controllers\ContextualUpload\VideoTutorialMakerEditController;
use IlBronza\VideoTutorial\Http\Controllers\ContextualUpload\VideoTutorialUploadController;
use IlBronza\VideoTutorial\Http\Controllers\Frontend\VideoTutorialReaderShowController;
use IlBronza\VideoTutorial\VideoTutorial;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::group([
	'middleware' => [
		'web',
		'auth',
		RoleMiddleware::class . ':' . VideoTutorial::MAKER_ROLE . ',web',
	],
	'prefix' => 'video-tutorials/contextual',
	'as' => config('videotutorial.routePrefix') . 'contextualUpload.',
	'routeTranslationPrefix' => VideoTutorial::getRouteTranslationPrefix(),
], function () {
	Route::post('', [
		VideoTutorialUploadController::class,
		'store',
	])->middleware('signed')->name('store');
	Route::get('{videotutorial}/edit', [
		VideoTutorialMakerEditController::class,
		'edit',
	])->name('edit');
	Route::put('{videotutorial}', [
		VideoTutorialMakerEditController::class,
		'update',
	])->name('update');
});

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'video-tutorials/show/',
	'as' => config('videotutorial.routePrefix') . 'frontend.',
	'routeTranslationPrefix' => VideoTutorial::getRouteTranslationPrefix(),
	],
	function()
	{
		Route::get('', [
			VideoTutorial::getController('videotutorial', 'frontend'),
			'index',
		])->name('videotutorials.index');
		Route::get('{videotutorial}', [
			VideoTutorialReaderShowController::class,
			'show',
		])->name('videotutorials.show');
	});

Route::group([
	'middleware' => ['web', 'videotutorial.roles'],
	'prefix' => 'manage-video-tutorials',
	'as' => config('videotutorial.routePrefix'),
	'routeTranslationPrefix' => VideoTutorial::getRouteTranslationPrefix(),
	],
	function()
	{
		Route::get('videotutorials-reorder/{videotutorial?}', [VideoTutorial::getController('videotutorial', 'reorder'), 'reorder'])->name('videotutorials.reorder');
		Route::post('videotutorials-reorder', [VideoTutorial::getController('videotutorial', 'reorder'), 'storeReorder'])->name('videotutorials.storeReorder');


		Route::delete('delete-media/{videotutorial}/{media}', [VideoTutorial::getController('videotutorial', 'deleteMedia'), 'deleteMedia'])->name('videotutorials.deleteMedia');



		$indexController = VideoTutorial::getController('videotutorial', 'index');
		if ($indexController === CRUDVideoTutorialController::class) {
			$indexController = CRUDVideoTutorialIndexController::class;
		}

		Route::get('', [
			$indexController,
			'index',
		])->name('videotutorials.index');
		Route::get('create', [VideoTutorial::getController('videotutorial', 'create'), 'create'])->name('videotutorials.create');
		Route::post('', [VideoTutorial::getController('videotutorial', 'store'), 'store'])->name('videotutorials.store');



		Route::get('{videotutorial}', [VideoTutorial::getController('videotutorial', 'show'), 'show'])->name('videotutorials.show');
		Route::get('{videotutorial}/edit', [VideoTutorial::getController('videotutorial', 'edit'), 'edit'])->name('videotutorials.edit');
		Route::put('{videotutorial}', [VideoTutorial::getController('videotutorial', 'edit'), 'update'])->name('videotutorials.update');

		Route::delete('{videotutorial}/delete', [VideoTutorial::getController('videotutorial', 'destroy'), 'destroy'])->name('videotutorials.destroy');

	});
