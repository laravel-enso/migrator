<?php

namespace LaravelEnso\Migrator\App\Database;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Menus\App\Models\Menu;
use LaravelEnso\Migrator\App\Services\Menus;
use LaravelEnso\Migrator\App\Services\ParentMenu;
use LaravelEnso\Migrator\App\Services\Permissions;
use LaravelEnso\Permissions\App\Models\Permission;

abstract class Migration extends LaravelMigration
{
    protected $permissions;
    protected $menu;
    protected $parentMenu;

    public function up()
    {
        DB::transaction(function () {
            (new Permissions($this->permissions))->handle();
            (new Menus($this->menu, $this->parentMenu))->handle();
        });
    }

    public function down()
    {
        DB::transaction(function () {
            if (isset($this->menu['name'])) {
                $this->removeMenu();
            }

            if (is_array($this->permissions)) {
                $this->removePermissions();
            }
        });
    }

    private function removeMenu()
    {
        $menu = Menu::whereName($this->menu['name'])
            ->when($this->parentMenu, fn ($query) => $query
                ->whereParentId((new ParentMenu($this->parentMenu))->id()))
            ->first();

        $menu->rolesWhereIsDefault()->update(['menu_id' => null]);
        $menu->delete();
    }

    private function removePermissions()
    {
        $permissions = (new Collection($this->permissions))->pluck('name');
        Permission::whereIn('name', $permissions)->delete();
    }
}
