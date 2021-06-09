<?php

namespace LaravelEnso\Migrator\Services;

use Illuminate\Support\Collection;
use LaravelEnso\Migrator\Exceptions\EnsoStructure;

class Validator
{
    public static function run(array $required, $attributes, string $element)
    {
        if (! is_array($attributes)) {
            throw EnsoStructure::invalidElement($element);
        }

        $diff = Collection::wrap($required)
            ->diff(Collection::wrap($attributes)->keys());

        if ($diff->isNotEmpty()) {
            throw EnsoStructure::missingAttributes($diff, $element);
        }
    }
}
