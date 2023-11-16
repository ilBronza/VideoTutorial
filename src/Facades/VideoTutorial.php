<?php

namespace IlBronza\VideoTutorial\Facades;

use Illuminate\Support\Facades\Facade;

class VideoTutorial extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'videotutorial';
    }
}
