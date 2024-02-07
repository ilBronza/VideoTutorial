<?php

use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use IlBronza\VideoTutorial\Http\Controllers\Frontend\CRUDVideoTutorialFrontendController;
use IlBronza\VideoTutorial\Http\Controllers\Frontend\CRUDVideoTutorialFrontendShowController;
use IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters\VideoTutorialCreateFieldsetsParameters;
use IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters\VideoTutorialShowFieldsetsParameters;
use IlBronza\VideoTutorial\Http\Parameters\TableFields\VideoTutorialFieldsGroupParametersFile;
use IlBronza\VideoTutorial\Models\Videotutorial;

return [
    'routePrefix' => 'videotutorial',

    'models' => [
        'videotutorial' => [
            'class' => Videotutorial::class,
            'table' => 'videotutorials__videotutorials',
            'fieldsGroupsFiles' => [
                'index' => VideoTutorialFieldsGroupParametersFile::class,
                // 'byClientIndex' => ByClientProductFieldsGroupParametersFile::class
            ],
            'relationshipsManagerClasses' => [
                // 'show' => ProductRelationManager::class
            ],
            'controllers' => [
                'frontend' => CRUDVideoTutorialFrontendController::class,
                'index' => CRUDVideoTutorialController::class,
                'create' => CRUDVideoTutorialController::class,
                'store' => CRUDVideoTutorialController::class,
                'show' => CRUDVideoTutorialFrontendShowController::class,
                'edit' => CRUDVideoTutorialController::class,
                'update' => CRUDVideoTutorialController::class,
                'destroy' => CRUDVideoTutorialController::class,
                'reorder' => CRUDVideoTutorialController::class,
                'replace' => CRUDVideoTutorialController::class,
                'deleteMedia' => CRUDVideoTutorialController::class,
            ],
            'parametersFiles' => [
                'create' => VideoTutorialCreateFieldsetsParameters::class,
                'show' => VideoTutorialShowFieldsetsParameters::class,
                // 'editAvatar' => UserdataAvatarEditFieldsetsParameters::class
            ],
        ]
    ]
];