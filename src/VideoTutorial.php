<?php

namespace IlBronza\VideoTutorial;

use IlBronza\CRUD\Providers\RouterProvider\RoutedObjectInterface;
use IlBronza\CRUD\Traits\CRUDNestableTrait;
use IlBronza\CRUD\Traits\IlBronzaPackages\IlBronzaPackagesTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class VideoTutorial implements RoutedObjectInterface
{
    public const MAKER_ROLE = 'videoTutrialMaker';

    use IlBronzaPackagesTrait;

    use CRUDNestableTrait;

    static $packageConfigPrefix = 'videotutorial';

    protected $modelInstance = null;

    public static function canUploadForPage(?Authenticatable $user): bool
    {
        return $user !== null && method_exists($user, 'hasRole') && $user->hasRole(self::MAKER_ROLE, 'web');
    }

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

    public function getSortableElements($modelInstance): Collection
    {
        return $this->getModelClass()::query()->visibleInCatalog()->get()
            ->filter(fn ($tutorial) => $tutorial->userCanSee(auth()->user()));
    }

    public function getFrontendMenuItems($modelInstance = null)
    {
        $elements = $modelInstance
            ? $this->parseTree($this->getSortableElements(null), $modelInstance->getKey())
            : $this->getSortableElementsTree();

        return $this->makeFrontendMenuItems($elements);
    }

    private function makeFrontendMenuItems(Collection $elements): array
    {
        return $elements->map(fn ($element) => [
            'href' => $element->getFrontendShowUrl(),
            'text' => $element->getName(),
            'children' => $this->makeFrontendMenuItems($element->childs ?? collect()),
        ])->values()->all();
    }
}
