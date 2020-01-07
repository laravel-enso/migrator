<?php

namespace LaravelEnso\Migrator\app\Services;

use LaravelEnso\Menus\app\Models\Menu;
use LaravelEnso\Migrator\app\Exceptions\ParentMenu;

class ParentMenuResolver
{
    private $segments;

    public function __construct(string $menu)
    {
        $this->segments = collect(explode('.', $menu));
    }

    public function handle()
    {
        $resolved = $this->matchingMenus()
            ->first(function ($menu) {
                return $this->indentify($menu);
            });

        if (! $resolved) {
            throw ParentMenu::invalid();
        }

        return $resolved;
    }

    private function indentify($menu)
    {
        return $this->segments->reverse()
            ->reduce(function ($match, $segment) {
                return $match !== null && optional($match->parent)->name === $segment
                    ? $match->parent
                    : null;
            }, $menu) !== null;
    }

    private function matchingMenus()
    {
        return Menu::isParent()
            ->whereName($this->segments->pop())
            ->get();
    }
}
