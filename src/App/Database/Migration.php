<?php

namespace LaravelEnso\Migrator\App\Database;

use Illuminate\Database\Migrations\Migration as LaravelMigration;
use Illuminate\Support\Facades\DB;
use LaravelEnso\Migrator\App\Services\Menus;
use LaravelEnso\Migrator\App\Services\Permissions;

abstract class Migration extends LaravelMigration
{
    protected array $permissions;
    protected array $menu;
    protected string $parentMenu;

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
