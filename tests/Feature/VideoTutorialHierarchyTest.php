<?php

namespace IlBronza\VideoTutorial\Tests\Feature;

use IlBronza\VideoTutorial\Facades\VideoTutorial as VideoTutorialRoutes;
use IlBronza\VideoTutorial\Models\Videotutorial;
use IlBronza\VideoTutorial\Tests\TestCase;

class VideoTutorialHierarchyTest extends TestCase
{
    public function test_administrator_can_set_and_clear_the_parent_through_the_crud_form(): void
    {
        $this->signIn('administrator');
        $parent = $this->tutorial('Padre');
        $deletedParent = $this->tutorial('Padre rimosso', [
            'deleted_at' => now(),
        ]);
        $fields = [
            'name' => 'Nuova guida',
            'title' => 'Nuova guida',
            'description' => 'Descrizione',
            'short_description' => 'Introduzione',
            'active' => true,
            'show' => true,
            'parent' => $parent->getKey(),
        ];

        $this->postJson(VideoTutorialRoutes::route('videotutorials.store'), $fields)->assertRedirect();
        $child = Videotutorial::where('name', $fields['name'])->firstOrFail();
        $this->assertSame($parent->getKey(), $child->parent_id);
        $this->assertArrayNotHasKey($child->getKey(), $parent->getParentPossibleValuesArray());
        $this->get($child->getEditUrl())->assertOk()->assertSee($parent->getKey());

        $this->putJson($parent->getUpdateUrl(), array_replace($fields, [
            'parent' => $child->getKey(),
        ]))->assertUnprocessable()->assertJsonValidationErrors('parent');
        $this->assertNull($parent->fresh()->parent_id);
        $this->assertSame('Padre', $parent->fresh()->name);

        $this->putJson($child->getUpdateUrl(), array_replace($fields, [
            'parent' => $deletedParent->getKey(),
        ]))->assertUnprocessable()->assertJsonValidationErrors('parent');

        $this->putJson($child->getUpdateUrl(), array_replace($fields, [
            'parent' => null,
        ]))->assertRedirect();
        $this->assertNull($child->fresh()->parent_id);
    }

    public function test_administrator_can_move_and_order_siblings(): void
    {
        $this->signIn('administrator');
        $parent = $this->tutorial('Padre');
        $sibling = $this->tutorial('Sorella', [
            'parent_id' => $parent->getKey(),
        ]);
        $moving = $this->tutorial('Da spostare');
        $url = VideoTutorialRoutes::route('videotutorials.storeReorder');

        $this->postJson($url, [
            'element_id' => $moving->getNestableKey(),
            'parent_id' => $parent->getNestableKey(),
            'siblings' => json_encode([$sibling->getNestableKey(), $moving->getNestableKey()]),
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertSame($parent->getKey(), $moving->fresh()->parent_id);
        $this->assertSame(0, $sibling->fresh()->sorting_index);
        $this->assertSame(1, $moving->fresh()->sorting_index);

        $this->postJson($url, [
            'element_id' => $moving->getNestableKey(),
            'parent_id' => 0,
        ])->assertOk();
        $this->assertNull($moving->fresh()->parent_id);
    }

    public function test_administrator_can_reorder_a_branch_and_delete_its_parent(): void
    {
        $this->signIn('administrator');
        $parent = $this->tutorial('Padre');
        $child = $this->tutorial('Figlia del ramo', [
            'parent_id' => $parent->getKey(),
        ]);
        $other = $this->tutorial('Ramo estraneo');

        $this->get($parent->getReorderUrl([
            'videotutorial' => $parent->getKey(),
        ]))->assertOk()->assertSee($child->getName())->assertDontSee($other->getName());

        $this->delete($parent->getDeleteUrl())->assertRedirect();
        $this->assertSoftDeleted($parent);
        $this->assertNull($child->fresh()->parent_id);
    }

    public function test_reorder_rejects_cycles_and_rolls_back_a_move_with_unrelated_siblings(): void
    {
        $this->signIn('administrator');
        $parent = $this->tutorial('Padre');
        $child = $this->tutorial('Figlia', [
            'parent_id' => $parent->getKey(),
            'sorting_index' => 5,
        ]);
        $other = $this->tutorial('Altra radice');
        $url = VideoTutorialRoutes::route('videotutorials.storeReorder');

        foreach ([$parent, $child] as $invalidParent) {
            $this->postJson($url, [
                'element_id' => $parent->getNestableKey(),
                'parent_id' => $invalidParent->getNestableKey(),
            ])->assertUnprocessable()->assertJsonValidationErrors('parent');
        }

        $this->postJson($url, [
            'element_id' => $child->getNestableKey(),
            'parent_id' => $other->getNestableKey(),
            'siblings' => json_encode([$child->getNestableKey(), $parent->getNestableKey()]),
        ])->assertUnprocessable()->assertJsonValidationErrors('siblings');

        $this->assertNull($parent->fresh()->parent_id);
        $this->assertSame($parent->getKey(), $child->fresh()->parent_id);
        $this->assertSame(5, $child->fresh()->sorting_index);
    }
}
