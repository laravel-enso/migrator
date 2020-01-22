<?php

namespace LaravelEnso\Migrator\App\Database;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Menus\App\Models\Menu;
use LaravelEnso\Migrator\App\Services\Menus;
use LaravelEnso\Migrator\App\Services\ParentMenu;
use LaravelEnso\Migrator\App\Services\Permissions;
use LaravelEnso\Migrator\App\Services\RemoveMenu;
use LaravelEnso\Migrator\App\Services\RemovePermissions;
use LaravelEnso\Permissions\App\Models\Permission;

abstract class Migration extends LaravelMigration
{
    protected $permissions;
    protected $menu;
    protected $parentMenu;

    public function up()
    {
        DB::transaction(function () {
            (new Permissions($this->permissions))->up();
            (new Menus($this->menu, $this->parentMenu))->up();
        });
    }

    public function down()
    {
        DB::transaction(function () {
            (new Menus($this->menu, $this->parentMenu))->down();
            (new Permissions($this->permissions))->down();
        });
    }
}
