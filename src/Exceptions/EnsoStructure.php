<?php

namespace LaravelEnso\Migrator\Exceptions;

use Illuminate\Support\Collection;
use InvalidArgumentException;

class EnsoStructure extends InvalidArgumentException
{
    public static function invalidElement(string $element)
    {
        return new self(__(
            'Invalid structure element ":element"',
            ['element' => $element]
        ));
    }

    public static function missingAttributes(Collection $diff, string $element)
    {
        return new self(__(
            'Mandatory attribute(s) ":attributes" missing from the current element ":element"',
            ['attributes' => $diff->implode(','), 'element' => $element]
        ));
    }

    public static function invalidParentMenu(string $menu)
    {
        return new self(
            __('The provided parent menu ":menu" was not found', ['menu' => $menu])
        );
    }
}
