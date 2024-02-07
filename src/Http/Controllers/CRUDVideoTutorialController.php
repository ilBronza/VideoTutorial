<?php

namespace IlBronza\VideoTutorial\Http\Controllers;

use IlBronza\CRUD\CRUD;
use IlBronza\CRUD\Models\Media;
use IlBronza\CRUD\Traits\CRUDBelongsToManyTrait;
use IlBronza\CRUD\Traits\CRUDCreateStoreTrait;
use IlBronza\CRUD\Traits\CRUDDeleteMediaTrait;
use IlBronza\CRUD\Traits\CRUDDeleteTrait;
use IlBronza\CRUD\Traits\CRUDDestroyTrait;
use IlBronza\CRUD\Traits\CRUDEditUpdateTrait;
use IlBronza\CRUD\Traits\CRUDIndexTrait;
use IlBronza\CRUD\Traits\CRUDNestableTrait;
use IlBronza\CRUD\Traits\CRUDPlainIndexTrait;
use IlBronza\CRUD\Traits\CRUDRelationshipTrait;
use IlBronza\CRUD\Traits\CRUDShowTrait;
use IlBronza\CRUD\Traits\CRUDUpdateEditorTrait;
use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CRUDVideoTutorialController extends CRUD
{
    use CRUDDeleteMediaTrait;
    use CRUDShowTrait;
    use CRUDPlainIndexTrait;
    use CRUDIndexTrait;
    use CRUDEditUpdateTrait;
    use CRUDUpdateEditorTrait;
    use CRUDCreateStoreTrait;

    use CRUDDeleteTrait;
    use CRUDDestroyTrait;

    use CRUDRelationshipTrait;
    use CRUDBelongsToManyTrait;

    use CRUDNestableTrait;
    /**
     * subject model class full path
     **/
    public $modelClass = Videotutorial::class;

    /**
     * http methods allowed. remove non existing methods to get a 403
     **/
    public $allowedMethods = [
        'index',
        'show',
        'edit',
        'update',
        'create',
        'store',
        'destroy',
        'deleted',
        'reorder',
        'storeReorder',
        'deleteMedia'
    ];

    public function getIndexFieldsArray()
    {
        return config('videotutorial.models.videotutorial.fieldsGroupsFiles.index')::getFieldsGroup();
    }

    public function getGenericParametersFile() : ? string
    {
        return config('videotutorial.models.videotutorial.parametersFiles.create');
    }

    public function getShowParametersFile() : ? string
    {
        return config('videotutorial.models.videotutorial.parametersFiles.show');
    }

    // public $parametersFile = VideotutorialParameters::class;
    public function getRouteBaseNamePrefix() : ? string
    {
        return config('videotutorial.routePrefix');
    }

    public function getModelClass() : string
    {
        return config("videotutorial.models.videotutorial.class");
    }
 
    public function getIndexElements()
    {
        return Videotutorial::all();
    }

    public function show(Videotutorial $videotutorial)
    {
        return $this->_show($videotutorial);
    }

    public function edit(Videotutorial $videotutorial)
    {
        return $this->_edit($videotutorial);
    }

    public function update(Request $request, Videotutorial $videotutorial)
    {
        return $this->_update($request, $videotutorial);
    }

    public function destroy(Videotutorial $videotutorial)
    {
        return $this->_destroy($videotutorial);
    }

    public function reorder(Request $request, Videotutorial $videotutorial = null)
    {
        return $this->_reorder($request, $videotutorial);
    }

    public function getSortableElements($modelInstance) : Collection
    {
        return $this->getModelClass()::all();
    }

    public function deleteMedia($videotutorial, Media $media)
    {
        return $this->_deleteMedia($videotutorial, $media);
    }
}

