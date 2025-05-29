<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreterConstructor;
use Fraction\Contracts\ShouldInterpreter;
use Illuminate\Container\Container;
use RuntimeException;

final class AsDefer implements ShouldInterpreter
{
    use ShareableInterpreterConstructor;

    public function handle(Container $container): mixed
    {
        if (! function_exists('Illuminate\Support\defer')) {
            throw new RuntimeException('Deferred actions should only be used in in Laravel 12 or later.');
        }

        $dependencies = $this->dependencies($container);

        \Illuminate\Support\defer(fn () => $dependencies->resolve($this->closure, $this->arguments));

        return true;
    }
}
