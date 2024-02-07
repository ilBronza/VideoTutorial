<?php

namespace IlBronza\VideoTutorial\Models;

use IlBronza\CRUD\Models\BaseModel;
use IlBronza\CRUD\Traits\Media\InteractsWithMedia;
use IlBronza\CRUD\Traits\Model\CRUDParentingTrait;
use IlBronza\CRUD\Traits\Model\CRUDUseUuidTrait;
use IlBronza\CRUD\Traits\Model\PackagedModelsTrait;
use IlBronza\VideoTutorial\Facades\VideoTutorial as FacadeVideoTutorial;
use Spatie\MediaLibrary\HasMedia;

class Videotutorial extends BaseModel implements HasMedia
{
	use CRUDUseUuidTrait;
    use InteractsWithMedia;

	use PackagedModelsTrait;
	use CRUDParentingTrait;

    public ? string $translationFolderPrefix = 'videotutorial';
	static $packageConfigPrefix = 'videotutorial';
	static $modelConfigPrefix = 'videotutorial';
	static $parentKeyName = 'parent_id';

	static $deletingRelationships = ['media'];

	public static function boot() {

		parent::boot();

	    static::deleting(function($model)
	    {
	        foreach($model->children()->get() as $child)
	        {
	            $child->parent_id = null;
	            $child->saveQuietly();
	        }
	    });
	}

	public function getIndexUrl(array $data = [])
	{
		return FacadeVideoTutorial::route('frontend.videotutorials.index');
	}

}