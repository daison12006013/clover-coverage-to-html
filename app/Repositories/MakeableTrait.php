<?php

namespace Daison\CloverToHtml\Repositories;

use ReflectionClass;

trait MakeableTrait
{
    public static function make(...$args)
    {
        $class = new ReflectionClass(static::class);

        $instance = $class->newInstanceArgs($args);

        return $instance->handle();
    }
}
