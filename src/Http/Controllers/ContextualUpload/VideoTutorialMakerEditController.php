<?php

namespace IlBronza\VideoTutorial\Http\Controllers\ContextualUpload;

use IlBronza\VideoTutorial\Facades\VideoTutorial as VideoTutorialRoutes;
use IlBronza\VideoTutorial\Http\Controllers\CRUDVideoTutorialController;
use IlBronza\VideoTutorial\Http\Parameters\FieldsetsParameters\VideoTutorialMakerFieldsetsParameters;
use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\Http\Request;

class VideoTutorialMakerEditController extends CRUDVideoTutorialController
{
    public $allowedMethods = [
        'edit',
        'update',
    ];

    public $avoidShowButton = true;

    public bool $showBackToList = false;

    public ?bool $updateEditor = false;

    public function getPackageConfigName(): string
    {
        return 'videotutorial';
    }

    public function getOverriddenEditParametersFile(): string
    {
        return VideoTutorialMakerFieldsetsParameters::class;
    }

    public function getUpdateModelAction(): string
    {
        return VideoTutorialRoutes::route('contextualUpload.update', [
            $this->getModel(),
        ]);
    }

    public function getAfterUpdatedRedirectUrl(): string
    {
        return VideoTutorialRoutes::route('contextualUpload.edit', [
            $this->getModel(),
        ]);
    }

    public function checkIfUserCanUpdate(): void
    {
        abort_unless($this->getModel()->context_uploader_id === (string) request()->user()->getAuthIdentifier(), 404);
    }

    public function requestHasRefreshRow(): bool
    {
        return false;
    }

    public function update(Request $request, Videotutorial $videotutorial)
    {
        // Only the metadata form can write through the maker route.
        $request->query->replace([]);
        $request->files->replace([]);
        $request->replace($request->only([
            'name',
            'title',
            'plain_short_description',
            'plain_description',
            'active',
            'show',
        ]));

        return $this->_update($request, $videotutorial);
    }
}
