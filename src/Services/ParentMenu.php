<?php

namespace LaravelEnso\Migrator\Services;

use Illuminate\Support\Collection;
use LaravelEnso\Menus\Models\Menu;
use LaravelEnso\Migrator\Exceptions\EnsoStructure;

class ParentMenu
{
    private string $menu;
    private Collection $segments;

    public function __construct(string $menu)
    {
        $this->menu = $menu;
        $this->segments = (new Collection(explode('.', $menu)));
    }

    public function id(): int
    {
        $found = $this->matches()
            ->first(fn ($menu) => $this->found($menu));

        if ($found) {
            return $found->id;
        }

        throw EnsoStructure::invalidParentMenu($this->menu);
    }

    private function found($menu): bool
    {
        return (bool) $this->segments->reverse()
            ->reduce(fn ($match, $segment) => $this->advance($match, $segment), $menu);
    }

    private function advance($match, $segment)
    {
        return $match && optional($match->parent)->name === $segment
            ? $match->parent
            : false;
    }

    private function matches(): Collection
    {
        return Menu::isParent()
            ->whereName($this->segments->pop())
            ->get();
    }
}
