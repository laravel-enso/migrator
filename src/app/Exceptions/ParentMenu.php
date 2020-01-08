<?php

namespace LaravelEnso\Migrator\App\Exceptions;

use InvalidArgumentException;

class ParentMenu extends InvalidArgumentException
{
    public static function invalid()
    {
        return new self(__('The provided parent menu does not appear to be correct'));
    }
}
