<?php

namespace IlBronza\VideoTutorial;

use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;

class VideoTutorial implements RoutedObjectInterface
{
    use IlBronzaPackagesTrait;

    static $packageConfigPrefix = 'videotutorial';

    public function manageMenuButtons()
    {
        if(! $menu = app('menu'))
            return;

        $settingsButton = $menu->provideSettingsButton();

        $videoTutorialButton = $menu->createButton([
            'name' => 'videotutorial',
            'icon' => 'camera',
            'text' => 'videotutorial::videotutorial.videotutorial'
        ]);

        $videoTutorialButton->addChild(
            $menu->createButton([
                    'name' => 'videotutorial.index',
                    'text' => 'videotutorial::videotutorial.index',
                    'icon' => 'user-lock',
                    'href' => $this->route('videotutorial.index'),
                    'roles' => ['administrator']
                ])
        );

        $videoTutorialButton->addChild(
            $menu->createButton([
                    'name' => 'videotutorial.index',
                    'text' => 'videotutorial::videotutorial.index',
                    'icon' => 'user-lock',
                    'href' => $this->route('videotutorial.index'),
                    'roles' => ['administrator']
                ])
        );

        $settingsButton->addChild(
            $videoTutorialButton
        );
    }
}