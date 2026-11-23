<?php

namespace IlBronza\VideoTutorial\Http\Controllers;

use IlBronza\CRUD\Http\Controllers\BasePackageController;
use IlBronza\CRUD\Http\Controllers\Traits\StandardTraits\PackageStandardIndexTrait;

class CRUDVideoTutorialIndexController extends BasePackageController
{
    use PackageStandardIndexTrait;

    protected static string $packageConfigPrefix = 'videotutorial';

    protected static string $modelConfigPrefix = 'videotutorial';

    public function getIndexElementsRelationsArray(): array
    {
        return [
            'parent',
            'children',
        ];
    }

    public function addIndexButtons(): void
    {
        $this->getTable()->addButton($this->getReorderButton());
    }
}
