<?php

namespace LaravelEnso\Migrator\App\Services;

use LaravelEnso\Menus\App\Models\Menu;
use LaravelEnso\Permissions\App\Models\Permission;

class Menus
{
    private const Attributes = ['name', 'icon', 'route', 'order_index', 'has_children'];

    private ?array $menu;
    private ?string $parent;

    public function __construct(?array $menu, ?string $parent)
    {
        $this->menu = $menu;
        $this->parent = $parent;
    }

    public function handle(): void
    {
        if ($this->menu === null) {
            return;
        }

        $this->validate()
            ->parent()
            ->permission()
            ->create();
    }

    private function parent(): self
    {
        if ($this->parent) {
            $this->menu['parent_id'] = (new ParentMenu($this->parent))->id();
        }

        return $this;
    }

    private function permission(): self
    {
        $permission = Permission::whereName($this->menu['route'])->first();

        $this->menu['permission_id'] = optional($permission)->id;

        unset($this->menu['route']);

        return $this;
    }

    private function create(): void
    {
        Menu::create($this->menu);
    }

    private function validate(): self
    {
        Validator::run(self::Attributes, $this->menu, 'menu');

        return $this;
    }
}
