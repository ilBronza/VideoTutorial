<?php

namespace IlBronza\VideoTutorial\Http\Controllers\Frontend;

use IlBronza\Form\Helpers\FieldsetsProvider\FieldsetParametersFile;
use IlBronza\VideoTutorial\Models\Videotutorial;
use Illuminate\View\View;

class VideoTutorialReaderShowController extends CRUDVideoTutorialFrontendShowController
{
    public ?bool $showEditLink = false;

    public $avoidShowButton = true;

    public function getShowParametersClass($passedModel = null): ?FieldsetParametersFile
    {
        $file = parent::getShowParametersClass($passedModel);
        if ($file === null) {
            return null;
        }

        $parameters = $file->getFieldsetsParameters();
        // The standard relationship renderer links to the administrative parent sheet.
        unset($parameters['base']['fields']['parent']);

        return FieldsetParametersFile::makeByParameters($parameters);
    }

    public function show(Videotutorial $videotutorial)
    {
        abort_unless($videotutorial->isVisibleInCatalog(), 404);
        abort_if(request()->ibeditor || $this->requestHasRefreshRow(), 404);

        $response = parent::show($videotutorial);

        if ($response instanceof View) {
            $response->with('canEditModelInstance', false);
        }

        return $response;
    }
}
