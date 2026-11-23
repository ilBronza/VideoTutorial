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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    use CRUDNestableTrait {
        getSortableElementsTree as private getFullSortableElementsTree;
    }
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
        return config('videotutorial.models.videotutorial.fieldsGroupsFiles.index')::getTracedFieldsGroup();
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

    public function reorder(Request $request, ?Videotutorial $videotutorial = null)
    {
        return $this->_reorder($request, $videotutorial);
    }

    public function getSortableElementsTree()
    {
        $this->modelInstance ??= null;

        if ($this->modelInstance === null) {
            return $this->getFullSortableElementsTree();
        }

        return $this->parseTree($this->getSortableElements($this->modelInstance), $this->modelInstance->getKey());
    }

    public function storeReorder(Request $request)
    {
        $payload = $request->validate([
            'element_id' => 'required|string',
            'siblings' => 'nullable|json',
        ]);

        $parentId = $request->input('parent_id');
        if (in_array($parentId, [
            null,
            '',
            '0',
            0,
        ], true)) {
            $parentId = null;
        }

        $siblings = json_decode($payload['siblings'] ?? '[]');
        $prefix = (string) config('crud.nestableLeadingId');
        $tutorialKey = fn ($value) => is_string($value) && str_starts_with($value, $prefix)
            ? substr($value, strlen($prefix))
            : $value;

        $keys = Validator::make([
            'element_id' => $tutorialKey($payload['element_id']),
            'parent_id' => $tutorialKey($parentId),
            'siblings' => is_array($siblings) ? array_map($tutorialKey, $siblings) : $siblings,
        ], [
            'element_id' => 'required|uuid',
            'parent_id' => 'nullable|uuid',
            'siblings' => 'present|array|list',
            'siblings.*' => 'required|uuid|distinct:ignore_case',
        ])->validate();

        $model = $this->makeModel();
        $tutorial = $model->getConnection()->transaction(function () use ($model, $keys) {
            $tutorial = $model->newQuery()->lockForUpdate()->findOrFail($keys['element_id']);
            $parentKey = $tutorial->getParentKeyName();
            $tutorial->{$parentKey} = $keys['parent_id'];
            $tutorial->save();

            $siblings = $model->newQuery()->where($parentKey, $keys['parent_id'])
                ->whereKey($keys['siblings'])->lockForUpdate()->get()
                ->keyBy(fn ($item) => Str::lower($item->getKey()));

            if ($siblings->count() !== count($keys['siblings'])) {
                throw ValidationException::withMessages([
                    'siblings' => __('videotutorial::videotutorial.invalidSiblings'),
                ]);
            }

            foreach ($keys['siblings'] as $position => $key) {
                $sibling = $siblings->get(Str::lower($key));
                $sibling->sorting_index = $position;
                $sibling->save();
            }

            return $tutorial;
        }, 3);

        return response()->json([
            'success' => true,
            'message' => 'Elemento ' . $tutorial->getName() . ' spostato correttamente',
        ]);
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

