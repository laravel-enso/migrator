<?php

namespace LaravelEnso\Migrator\app\Services\Creators;

use LaravelEnso\Menus\app\Models\Menu;
use LaravelEnso\Migrator\app\Services\AttributeValidator;
use LaravelEnso\Migrator\app\Services\ParentMenuResolver;
use LaravelEnso\Permissions\app\Models\Permission;

class MenuCreator
{
    private const Attributes = ['name', 'icon', 'route', 'order_index', 'has_children'];

    private $menu;
    private $parentMenu;

    public function __construct(?array $menu)
    {
        $this->menu = $menu;
    }

    public function handle()
    {
        if ($this->isValid()) {
            $this->permission()
                ->create();
        }
    }

    public function parent(?string $menu)
    {
        if (! empty($menu)) {
            $this->parentMenu = (new ParentMenuResolver($menu))->handle();
        }

        return $this;
    }

    private function permission()
    {
        $this->menu['permission_id'] = optional(
            Permission::whereName($this->menu['route'])
                ->first()
        )->id;

        unset($this->menu['route']);

        return $this;
    }

    private function create()
    {
        Menu::create($this->menu + [
            'parent_id' => optional($this->parentMenu)->id,
        ]);
    }

    private function isValid()
    {
        return $this->menu !== null
            && AttributeValidator::passes(self::Attributes, $this->menu);
    }
}
