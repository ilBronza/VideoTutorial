<?php

return [
    'routePrefix' => 'videotutorial',

    'models' => [
        'videotutorial' => [
            'class' => Videotutorial::class,
            'table' => 'videotutorials__videotutorials',
            'fieldsGroupsFiles' => [
                // 'index' => ProductFieldsGroupParametersFile::class,
                // 'byClientIndex' => ByClientProductFieldsGroupParametersFile::class
            ],
            'relationshipsManagerClasses' => [
                // 'show' => ProductRelationManager::class
            ],
            'controllers' => [
                // 'admin' => AdminUserDataController::class,
                // 'edit' => EditUserDataController::class,
                // 'update' => EditUserDataController::class,
                // 'editAvatar' => EditUserDataAvatarController::class,
                // 'updateAvatar' => EditUserDataAvatarController::class,
                // 'deleteMedia' => UserDataDeleteMediaController::class
            ],
            'parametersFiles' => [
                // 'edit' => UserdataEditFieldsetsParameters::class,
                // 'editAvatar' => UserdataAvatarEditFieldsetsParameters::class
            ],
        ]
    ]
];