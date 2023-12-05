<?php

namespace IlBronza\VideoTutorial\Models;

use IlBronza\CRUD\Models\SluggableBaseModel;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;

class Videotutorial extends SluggableBaseModel
{
	use PackagedModelsTrait;
	use CRUDParentingTrait;

	static $packageConfigPrefix = 'videotutorial';
	static $modelConfigPrefix = 'videotutorial';
	static $parentKeyName = 'parent_slug';

	static $deletingRelationships = [];

	public static function boot() {

		parent::boot();

	    static::deleting(function($model)
	    {
	        foreach($model->children()->get() as $child)
	        {
	            $child->parent_slug = null;
	            $child->saveQuietly();
	        }
	    });
	}

}