<?php

namespace LaravelEnso\Migrator\app\Services;

use LaravelEnso\Migrator\app\Exceptions\EnsoStructure;

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
