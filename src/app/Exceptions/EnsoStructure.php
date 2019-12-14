<?php

namespace LaravelEnso\Migrator\app\Exceptions;

use InvalidArgumentException;

class EnsoStructure extends InvalidArgumentException
{
    public static function invalid()
    {
        return new self(__(
            'The current structure element is wrongly defined. Check the exception trace below'
        ));
    }
}
