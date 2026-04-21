<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Menus\Models\Menu;
use LaravelEnso\Migrator\Exceptions\EnsoStructure;
use LaravelEnso\Migrator\Services\ParentMenu;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigratorParentMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());
    }

    #[Test]
    public function resolves_a_dot_notated_parent_menu_chain(): void
    {
        $administration = Menu::create([
            'name' => 'administration',
            'icon' => 'fal users-cog',
            'order_index' => 1,
            'has_children' => true,
        ]);

        $settings = Menu::create([
            'parent_id' => $administration->id,
            'name' => 'settings',
            'icon' => 'fal cog',
            'order_index' => 2,
            'has_children' => true,
        ]);

        $this->assertSame($settings->id, (new ParentMenu('administration.settings'))->id());
    }

    #[Test]
    public function throws_when_parent_menu_chain_cannot_be_resolved(): void
    {
        $this->expectException(EnsoStructure::class);
        $this->expectExceptionMessage('The provided parent menu "administration.settings" was not found');

        Menu::create([
            'name' => 'settings',
            'icon' => 'fal cog',
            'order_index' => 1,
            'has_children' => true,
        ]);

        (new ParentMenu('administration.settings'))->id();
    }
}
