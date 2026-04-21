<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Menus\Models\Menu;
use LaravelEnso\Migrator\Services\Menus;
use LaravelEnso\Permissions\Models\Permission;
use LaravelEnso\Roles\Models\Role;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigratorMenusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());
    }

    #[Test]
    public function creates_a_menu_with_parent_and_permission_resolution(): void
    {
        $parent = Menu::create([
            'name' => 'administration',
            'icon' => 'fal users-cog',
            'order_index' => 1,
            'has_children' => true,
        ]);

        Permission::create([
            'name' => 'migrator.index',
            'description' => 'Migrator index',
            'is_default' => true,
        ]);

        (new Menus([
            'name' => 'Migrator',
            'icon' => 'fal shuttle-space',
            'route' => 'migrator.index',
            'order_index' => 10,
            'has_children' => false,
        ], 'administration'))->up();

        $menu = Menu::whereName('Migrator')->firstOrFail();

        $this->assertSame($parent->id, $menu->parent_id);
        $this->assertSame(
            Permission::whereName('migrator.index')->firstOrFail()->id,
            $menu->permission_id
        );
    }

    #[Test]
    public function destroys_menu_and_clears_default_role_menu_id(): void
    {
        $permission = Permission::create([
            'name' => 'migrator.destroy',
            'description' => 'Destroy menu',
            'is_default' => true,
        ]);

        (new Menus([
            'name' => 'Destroyable',
            'icon' => 'fal trash',
            'route' => 'migrator.destroy',
            'order_index' => 11,
            'has_children' => false,
        ], null))->up();

        $menu = Menu::whereName('Destroyable')->firstOrFail();
        $defaultRole = Role::whereName(config('enso.config.defaultRole'))->firstOrFail();
        $defaultRole->update(['menu_id' => $menu->id]);

        (new Menus([
            'name' => 'Destroyable',
            'icon' => 'fal trash',
            'route' => 'migrator.destroy',
            'order_index' => 11,
            'has_children' => false,
        ], null))->down();

        $this->assertNull($defaultRole->fresh()->menu_id);
        $this->assertDatabaseMissing('menus', ['name' => 'Destroyable']);
        $this->assertDatabaseHas('permissions', ['id' => $permission->id]);
    }

    #[Test]
    public function ignores_empty_menu_payloads(): void
    {
        (new Menus([], null))->up();
        (new Menus([], null))->down();

        $this->assertTrue(true);
    }
}
