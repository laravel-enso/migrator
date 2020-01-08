<?php

namespace LaravelEnso\Migrator\App\Services;

use LaravelEnso\Migrator\App\Exceptions\EnsoStructure;

class AttributeValidator
{
    public static function passes(array $required, array $attributes)
    {
        $valid = count($required) === count($attributes)
            && collect($attributes)->keys()
                ->diff(collect($required)->values())
                ->isEmpty();

        if (! $valid) {
            throw EnsoStructure::invalid();
        }

        return $valid;
    }
}
