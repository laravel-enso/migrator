<?php

namespace LaravelEnso\Migrator\app\Services\Creators;

use LaravelEnso\Migrator\app\Services\AttributeValidator;
use LaravelEnso\Permissions\app\Models\Permission;
use LaravelEnso\Roles\app\Models\Role;

class PermissionCreator
{
    private const Attributes = ['name', 'description', 'type', 'is_default'];

    private $permissions;
    private $roleIds;
    private $defaultRoleId;

    public function __construct(?array $permissions)
    {
        $this->permissions = $permissions;
    }

    public function handle()
    {
        if ($this->isValid()) {
            $this->setRoleIds()
                ->setDefaultRoleId()
                ->create();
        }
    }

    private function create()
    {
        collect($this->permissions)
            ->each(function ($permission) {
                Permission::create($permission)
                    ->roles()
                    ->attach($this->roles($permission));
            });
    }

    private function roles($permission)
    {
        return $permission['is_default']
            ? $this->roleIds
            : $this->defaultRoleId;
    }

    private function setRoleIds()
    {
        $this->roleIds = Role::pluck('id');

        return $this;
    }

    private function setDefaultRoleId()
    {
        $this->defaultRoleId = optional(
            Role::whereName(
                config('enso.config.defaultRole')
            )->first()
        )->id;

        return $this;
    }

    private function isValid()
    {
        return $this->permissions !== null
            && collect($this->permissions)
                ->filter(function ($permission) {
                    return ! AttributeValidator::passes(self::Attributes, $permission);
                })->isEmpty();
    }
}
