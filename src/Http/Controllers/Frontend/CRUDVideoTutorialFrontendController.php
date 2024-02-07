<?php

namespace IlBronza\VideoTutorial\Http\Controllers\Frontend;

use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\Http\Request;

class CRUDVideoTutorialFrontendController extends CRUDVideoTutorialController
{
    public $allowedMethods = [
        'index',
        'show'
    ];

    public $nestableElementViewName = 'crud::nestable.show.element_nestable';

    public function index(Request $request)
    {
        $this->modelInstance = $modelInstance = null;

        $elements = $this->getSortableElementsTree();

        //obtain action, rootUrl and ParentUrl
        extract($this->getSortableUrls());

        $maxDepth = $this->getMaxReorderDepth();

        $nestableElementViewName = $this->getNestableElementViewName();

        return view('crud::nestable.show.index', compact('nestableElementViewName', 'modelInstance', 'rootUrl', 'parentUrl', 'elements', 'action', 'maxDepth'));
    }

    public function show(Videotutorial $videotutorial)
    {
        return $this->_show($videotutorial);
    }
}

