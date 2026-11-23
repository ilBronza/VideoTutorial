<?php

namespace IlBronza\VideoTutorial\Tests\Fixtures;

class ProfileMenu
{
    public function manageMenuButtons(): void
    {
        $menu = app('menu');
        $menu->provideButton([
            'name' => 'account',
            'text' => 'Profile',
            'navbar' => $menu->provideMainRightBar()->getName(),
        ])->addChildFromArray([
            'name' => 'account.info',
            'text' => 'My profile',
        ]);
    }
}
