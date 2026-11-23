<?php

namespace IlBronza\VideoTutorial\Tests\Feature;

use IlBronza\VideoTutorial\Facades\VideoTutorial as VideoTutorialRoutes;
use IlBronza\VideoTutorial\Models\Videotutorial;
use IlBronza\VideoTutorial\Tests\TestCase;

class VideoTutorialAccessTest extends TestCase
{
    public function test_guests_cannot_open_the_catalog_or_a_published_tutorial(): void
    {
        $tutorial = $this->tutorial('Guida pubblicata');

        $this->getJson(VideoTutorialRoutes::route('frontend.videotutorials.index'))->assertUnauthorized();
        $this->getJson($tutorial->getFrontendShowUrl())->assertUnauthorized();
    }

    public function test_reader_can_follow_catalog_links_but_cannot_open_unpublished_tutorials(): void
    {
        $this->signIn('reader');
        $root = $this->tutorial('Radice pubblicata');
        $child = $this->tutorial('Figlia pubblicata', [
            'parent_id' => $root->getKey(),
        ]);
        $hidden = $this->tutorial('Padre riservato', [
            'show' => false,
        ]);
        $orphan = $this->tutorial('Pubblicata con padre riservato', [
            'parent_id' => $hidden->getKey(),
        ]);
        $inactive = $this->tutorial('Guida disattivata', [
            'active' => false,
        ]);
        $legacy = $this->tutorial('Guida con visibilità non impostata', [
            'show' => null,
        ]);
        $deleted = $this->tutorial('Guida rimossa', [
            'deleted_at' => now(),
        ]);

        $catalog = $this->get(VideoTutorialRoutes::route('frontend.videotutorials.index'))->assertOk();
        $catalog->assertSee($child->getFrontendShowUrl())->assertSee($orphan->getFrontendShowUrl());
        $catalog->assertDontSee($child->getShowUrl());

        $elements = $catalog->viewData('elements')->keyBy(fn (Videotutorial $item) => $item->getKey());
        $this->assertEqualsCanonicalizing([$root->getKey(), $orphan->getKey()], $elements->keys()->all());
        $this->assertSame([$child->getKey()], $elements->get($root->getKey())->childs->pluck('id')->all());

        foreach ([$hidden, $inactive, $legacy, $deleted] as $unpublished) {
            $catalog->assertDontSee($unpublished->getName());
            $this->get($unpublished->getFrontendShowUrl())->assertNotFound();
        }

        $this->get($child->getFrontendShowUrl())
            ->assertOk()
            ->assertSee($child->getName())
            ->assertDontSee($child->getEditUrl());
        $this->get($orphan->getFrontendShowUrl())
            ->assertOk()
            ->assertDontSee($hidden->getName());
    }

    public function test_reader_cannot_use_administrative_routes(): void
    {
        $this->signIn('reader');
        $tutorial = $this->tutorial('Guida pubblicata');

        $this->get($tutorial->getShowUrl())->assertForbidden();
        $this->get($tutorial->getEditUrl())->assertForbidden();
        $this->putJson($tutorial->getUpdateUrl())->assertForbidden();
        $this->postJson(VideoTutorialRoutes::route('videotutorials.storeReorder'))->assertForbidden();
    }

    public function test_administrator_can_open_a_hidden_tutorial_in_management(): void
    {
        $this->signIn('administrator');
        $hidden = $this->tutorial('Bozza amministrativa', [
            'show' => false,
        ]);

        $indexUrl = VideoTutorialRoutes::route('videotutorials.index');
        $this->get($indexUrl)->assertOk()->assertSee(VideoTutorialRoutes::route('videotutorials.reorder'));
        $this->getJson($indexUrl, [
            'X-Requested-With' => 'XMLHttpRequest',
        ])->assertOk()->assertSee($hidden->getName());
        $this->get($hidden->getShowUrl())->assertOk()->assertSee($hidden->getName());
        $this->get($hidden->getFrontendShowUrl())->assertNotFound();
    }
}
