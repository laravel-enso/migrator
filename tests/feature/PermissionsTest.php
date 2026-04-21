<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use LaravelEnso\Migrator\Services\Permissions;
use LaravelEnso\Permissions\Models\Permission;
use LaravelEnso\Roles\Models\Role;
use LaravelEnso\Users\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MigratorPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());
    }

    #[Test]
    public function creates_permissions_and_assigns_them_to_expected_roles(): void
    {
        $service = new Permissions([
            [
                'name' => 'migrator.default',
                'description' => 'Default permission',
                'is_default' => true,
            ],
            [
                'name' => 'migrator.restricted',
                'description' => 'Restricted permission',
                'is_default' => false,
            ],
        ]);

        $service->up();

        $defaultPermission = Permission::whereName('migrator.default')->firstOrFail();
        $restrictedPermission = Permission::whereName('migrator.restricted')->firstOrFail();
        $defaultRole = Role::whereName(config('enso.config.defaultRole'))->firstOrFail();
        $allRoleIds = Role::pluck('id')->sort()->values()->all();

        $this->assertSame(
            $allRoleIds,
            $defaultPermission->roles()->pluck('roles.id')->sort()->values()->all()
        );
        $this->assertSame(
            [$defaultRole->id],
            $restrictedPermission->roles()->pluck('roles.id')->sort()->values()->all()
        );
    }

    #[Test]
    public function removes_declared_permissions_on_down(): void
    {
        $service = new Permissions([
            [
                'name' => 'migrator.to-delete',
                'description' => 'To delete',
                'is_default' => false,
            ],
        ]);

        $service->up();

        $this->assertDatabaseHas('permissions', ['name' => 'migrator.to-delete']);

        $service->down();

        $this->assertDatabaseMissing('permissions', ['name' => 'migrator.to-delete']);
    }

    #[Test]
    public function ignores_empty_permission_lists(): void
    {
        (new Permissions([]))->up();
        (new Permissions([]))->down();

        $this->assertTrue(true);
    }
}
