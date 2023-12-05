<?php

namespace IlBronza\VideoTutorial\Http\Controllers;

use IlBronza\CRUD\CRUD;

class CRUDVideoTutorialController extends CRUD
{
    public static $tables = [

        'index' => [
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfEdit' => 'links.edit',

                'active' => 'boolean',

                'created_at' => 'dates.date',

                'name' => 'flat',
                'slug' => 'flat',

                'options' => 'relations.belongsToMany',
                'drives_count' => 'flat',

                'old_barcode_label' => 'editor.text',
                'barcode_label' => 'editor.text',
                // 'short_name' => 'flat',
                'description' => 'editor.text',

                'input' => 'flat',
                'output' => 'flat',
                'general' => 'flat',

                'mySelfIsRoot.parent_slug' => 'notBoolean',
                'children' => 'relations.hasMany',
                // 'active' => 'editor.toggle',

                'mySelfDelete' => 'links.delete'
            ]
        ],
        'related' => [
            'fields' => 
            [
                'mySelfPrimary' => 'primary',
                'mySelfEdit' => 'links.edit',

                'active' => 'boolean',
                'created_at' => 'dates.date',

                'name' => 'flat',
                'slug' => 'flat',

                'input' => 'flat',
                'output' => 'flat',
                'general' => 'flat',

                'mySelfIsRoot.parent_slug' => 'notBoolean',
                'children' => 'relations.hasMany',
                // 'active' => 'editor.toggle',

                'mySelfDelete' => 'links.delete'
            ]
        ]
    ];

    static $formFields = [
        'common' => [
            'general' => [
                'fields' => [
                    'parent' => [
                        'type' => 'select',
                        'multiple' => false,
                        'rules' => 'string|nullable|exists:videotutorials,slug',
                        'relation' => 'parent'
                    ],

                    'name' => ['text' => 'string|required|max:255'],
                    'active' => ['boolean' => 'boolean|required'],
                    'hide' => ['boolean' => 'boolean|nullable'],
                    // 'options' => [
                    //     'type' => 'select',
                    //     'multiple' => true,
                    //     'rules' => 'array|nullable|exists:opt__options,id',
                    //     'relation' => 'options'
                    // ]
                ],
                'width' => ['1-2@m']
            ],
            'data' => [
                'fields' => [
                    'internal_id' => ['number' => 'numeric|nullable'],
                    'old_barcode_label' => ['text' => 'string|nullable|max:9'],
                    'barcode_label' => ['text' => 'string|nullable|max:9'],
                    'description' => ['textarea' => 'string|nullable|max:10240'],
                ],
                'width' => ['1-2@m']
            ],
            'testing' => [
                'fields' => [
                    'input' => ['text' => 'string|nullable|max:255'],
                    'output' => ['text' => 'string|nullable|max:255'],
                    'general' => ['text' => 'string|nullable|max:255'],
                ],
                'width' => ['1-1@m']
            ],
        ]
    ];    

    public $relationshipsManagerClass = VideotutorialRelationManager::class;

    // public $parametersFile = VideotutorialParameters::class;

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

    public function getNestableElementViewName()
    {
        return 'models.videotutorials._element_nestable';
    }

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
        'replaceElement',
        'storeReplaceElement'
    ];

    // public $parametersFile = VideotutorialParameters::class;
 
    public function getIndexElements()
    {
        return Videotutorial::withCount('drives')->get();
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
        return $this->getModelClass()::active()->withCount('drives')->get();
    }

    public function replaceElement(Request $request, Videotutorial $videotutorial)
    {
        return $this->_replaceElement($request, $videotutorial);
    }

    public function storeReplaceElement(Request $request, Videotutorial $videotutorial)
    {
        return $this->_storeReplaceElement($request, $videotutorial);
    }

}

