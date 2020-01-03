<?php

namespace LaravelEnso\Migrator\App\Services;

use Illuminate\Support\Collection;
use LaravelEnso\Permissions\App\Models\Permission;
use LaravelEnso\Roles\App\Models\Role;

class Permissions
{
    private const Attributes = ['name', 'description', 'type', 'is_default'];

    private Collection $permissions;
    private Collection $roleIds;
    private ?int $defaultRoleId;

    public function __construct(?array $permissions)
    {
        $this->permissions = new Collection($permissions);
    }

    public function handle()
    {
        if ($this->permissions->isEmpty()) {
            return;
        }

        $this->validate()
            ->roleIds()
            ->defaultRoleId()
            ->permissions();
    }

    private function permissions(): void
    {
        $this->permissions->each(fn ($permission) => $this->create($permission));
    }

    private function create($permission): void
    {
        Permission::create($permission)
            ->roles()->attach($this->roles($permission));
    }

    private function roles($permission)
    {
        return $permission['is_default']
            ? $this->roleIds
            : $this->defaultRoleId;
    }

    private function roleIds(): self
    {
        $this->roleIds = Role::pluck('id');

        return $this;
    }

    private function defaultRoleId(): self
    {
        $role = Role::whereName(config('enso.config.defaultRole'))->first();

        $this->defaultRoleId = optional($role)->id;

        return $this;
    }

    private function validate(): self
    {
        $this->permissions->each(fn ($permission) => Validator::run(
            self::Attributes, $permission, 'permissions')
        );

        return $this;
    }
}
