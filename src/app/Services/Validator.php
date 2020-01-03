<?php

namespace LaravelEnso\Migrator\App\Services;

use Illuminate\Support\Collection;
use LaravelEnso\Migrator\App\Exceptions\EnsoStructure;

class Validator
{
    public static function run(array $required, $attributes, string $element)
    {
        if (! is_array($attributes)) {
            throw EnsoStructure::invalidElement($element);
        }

        $diff = (new Collection($required))
            ->diff((new Collection($attributes))->keys());

        if ($diff->isNotEmpty()) {
            throw EnsoStructure::missingAttributes($diff, $element);
        }
    }
}
