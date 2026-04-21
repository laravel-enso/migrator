<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Menus\Models\Menu;
use LaravelEnso\Migrator\Database\Migration;
use LaravelEnso\Permissions\Models\Permission;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigratorMigrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());
    }

    #[Test]
    public function orchestrates_permission_and_menu_creation_and_removal(): void
    {
        $migration = new class extends Migration {
            protected array $permissions = [
                [
                    'name' => 'migrator.page',
                    'description' => 'Migrator page',
                    'is_default' => true,
                ],
            ];

            protected array $menu = [
                'name' => 'Migrator Page',
                'icon' => 'fal wrench',
                'route' => 'migrator.page',
                'order_index' => 100,
                'has_children' => false,
            ];
        };

        $migration->up();

        $this->assertDatabaseHas('permissions', ['name' => 'migrator.page']);
        $this->assertDatabaseHas('menus', ['name' => 'Migrator Page']);

        $permission = Permission::whereName('migrator.page')->firstOrFail();
        $menu = Menu::whereName('Migrator Page')->firstOrFail();

        $this->assertSame($permission->id, $menu->permission_id);

        $migration->down();

        $this->assertDatabaseMissing('menus', ['name' => 'Migrator Page']);
        $this->assertDatabaseMissing('permissions', ['name' => 'migrator.page']);
    }
}
