<?php

namespace IlBronza\VideoTutorial;

use IlBronza\Buttons\Button;
use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\CRUDNestableTrait;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;

class VideoTutorial implements RoutedObjectInterface
{
    use IlBronzaPackagesTrait;

    use CRUDNestableTrait;

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
                    'href' => $this->route('videotutorials.index'),
                    'roles' => ['administrator']
                ])
        );

        $settingsButton->addChild(
            $videoTutorialButton
        );
    }

    protected function getModelClass()
    {
        return config(static::getPackageConfigPrefix() . ".models.videotutorial.class");
    }

    public function getFrontendMenuItems($modelInstance = null)
    {
        $this->modelInstance = $modelInstance;

        $result = [];

        $elements = $this->getSortableElementsTree();

        foreach($elements as $element)
        {
            $item = [
                'href' => 'asdasd' . $element->getKey(),
                'text' => $element->getName(),
                'children' => $this->getFrontendMenuItems($element)
            ];

            $result[] = $item;
        }

        return $result;
    }
}