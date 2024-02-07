<?php

use IlBronza\VideoTutorial\VideoTutorial;

Route::group([
	'middleware' => ['web', 'auth'],
	'prefix' => 'video-tutorials/show/',
	'as' => config('videotutorial.routePrefix') . 'frontend.'
	],
	function()
	{
		Route::get('', [VideoTutorial::getController('videotutorial', 'frontend'), 'index'])->name('videotutorials.index');		
	});

Route::group([
	'middleware' => ['web', 'role:administrator'],
	'prefix' => 'manage-video-tutorials',
	'as' => config('videotutorial.routePrefix')
	],
	function()
	{
		Route::get('videotutorials-reorder/{videotutorial?}', [VideoTutorial::getController('videotutorial', 'reorder'), 'reorder'])->name('videotutorials.reorder');
		Route::post('videotutorials-reorder', [VideoTutorial::getController('videotutorial', 'reorder'), 'storeReorder'])->name('videotutorials.storeReorder');


		Route::delete('delete-media/{videotutorial}/{media}', [VideoTutorial::getController('videotutorial', 'deleteMedia'), 'deleteMedia'])->name('videotutorials.deleteMedia');



		Route::get('', [VideoTutorial::getController('videotutorial', 'index'), 'index'])->name('videotutorials.index');
		Route::get('create', [VideoTutorial::getController('videotutorial', 'create'), 'create'])->name('videotutorials.create');
		Route::post('', [VideoTutorial::getController('videotutorial', 'store'), 'store'])->name('videotutorials.store');



		Route::get('{videotutorial}', [VideoTutorial::getController('videotutorial', 'show'), 'show'])->name('videotutorials.show');
		Route::get('{videotutorial}/edit', [VideoTutorial::getController('videotutorial', 'edit'), 'edit'])->name('videotutorials.edit');
		Route::put('{videotutorial}', [VideoTutorial::getController('videotutorial', 'edit'), 'update'])->name('videotutorials.update');

		Route::delete('{videotutorial}/delete', [VideoTutorial::getController('videotutorial', 'destroy'), 'destroy'])->name('videotutorials.destroy');

	});