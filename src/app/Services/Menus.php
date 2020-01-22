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

    public function up(): void
    {
        if (! $this->menu) {
            return;
        }

        $this->validate()
            ->parent()
            ->permission()
            ->create();
    }

    public function down()
    {
        if (! $this->menu) {
            return;
        }

        $this->validate()
            ->destroy();
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

    private function destroy(): void
    {
        $menu = Menu::whereName($this->menu['name'])
            ->when($this->parent, fn ($query) => $query
                ->whereParentId((new ParentMenu($this->parent))->id()))
            ->first();

        $menu->rolesWhereIsDefault()->update(['menu_id' => null]);
        $menu->delete();
    }

    private function validate(): self
    {
        Validator::run(self::Attributes, $this->menu, 'menu');

        return $this;
    }
}
