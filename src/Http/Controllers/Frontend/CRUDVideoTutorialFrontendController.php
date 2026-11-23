<?php

namespace IlBronza\VideoTutorial\Http\Controllers\Frontend;

use IlBronza\VideoTutorial\Facades\VideoTutorial;
use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CRUDVideoTutorialFrontendController extends CRUDVideoTutorialController
{
    public $allowedMethods = [
        'index',
    ];

    public $nestableElementViewName = 'videotutorial::catalog-element';

    public function index(Request $request)
    {
        $this->modelInstance = null;

        return view('crud::nestable.show.index', [
            'nestableElementViewName' => $this->getNestableElementViewName(),
            'modelInstance' => null,
            'rootUrl' => null,
            'parentUrl' => null,
            'elements' => $this->getSortableElementsTree(),
        ]);
    }

    public function getSortableElements($modelInstance): Collection
    {
        return VideoTutorial::getSortableElements($modelInstance);
    }
}
