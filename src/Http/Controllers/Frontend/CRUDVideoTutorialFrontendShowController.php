<?php

namespace IlBronza\VideoTutorial\Http\Controllers\Frontend;

use IlBronza\VideoTutorial\Facades\VideoTutorial as FacadeVideoTutorial;
use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use IlBronza\VideoTutorial\Models\Videotutorial;

class CRUDVideoTutorialFrontendShowController extends CRUDVideoTutorialController
{
    public $allowedMethods = [
        'show',
        'index'
    ];

    public function getShowParametersFile() : ? string
    {
        return config('videotutorial.models.videotutorial.parametersFiles.show');
    }

    public function getIndexUrl()
    {
        return FacadeVideoTutorial::route('frontend.videotutorials.index');
    }

    public function show(Videotutorial $videotutorial)
    {
        return $this->_show($videotutorial);
    }
}

