<?php

namespace LaravelEnso\Migrator\app\Database;

use Illuminate\Support\Facades\DB;
use LaravelEnso\Menus\app\Models\Menu;
use LaravelEnso\Permissions\app\Models\Permission;
use LaravelEnso\Migrator\app\Services\Creators\MenuCreator;
use Illuminate\Database\Migrations\Migration as BaseMigration;
use LaravelEnso\Migrator\app\Services\Creators\PermissionCreator;

abstract class Migration extends BaseMigration
{
    protected $parentMenu;
    protected $menu;
    protected $permissions;

    public function up()
    {
        DB::transaction(function () {
            (new PermissionCreator($this->permissions))->handle();

            (new MenuCreator($this->menu))
                ->parent($this->parentMenu)
                ->handle();
        });
    }

    public function down()
    {
        DB::transaction(function () {
            if (isset($this->menu['name'])) {
                Menu::whereName($this->menu)
                    ->delete();
            }

            Permission::whereIn(
                'name', collect($this->permissions)->pluck('name')
            )->delete();
        });
    }
}
