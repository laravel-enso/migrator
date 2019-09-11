<?php

namespace LaravelEnso\Migrator\app\Services;

use LaravelEnso\Migrator\app\Exceptions\EnsoStructureException;

class AttributeValidator
{
    public static function passes(array $required, array $attributes)
    {
        $valid = count($required) === count($attributes)
            && collect($attributes)->keys()
                ->diff(collect($required)->values())
                ->isEmpty();

        if (! $valid) {
            throw new EnsoStructureException(__(
                'The current structure element is wrongly defined. Check the exception trace below'
            ));
        }

        return $valid;
    }
}
