<?php

namespace IlBronza\VideoTutorial\Http\Middleware;

use IlBronza\CRUD\Middleware\CRUDBasePackageMiddlewareRolesPermissions;

/**
 * Resolves allowed roles for VideoTutorial routes from config (videotutorial.defaultRoles / videotutorial.routeRoles).
 */
class VideoTutorialMiddlewareRolesPermissions extends CRUDBasePackageMiddlewareRolesPermissions
{
    protected string $configPackageName = 'videotutorial';
}
