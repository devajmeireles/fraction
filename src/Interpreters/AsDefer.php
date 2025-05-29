<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreterConstructor;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;

use function Illuminate\Support\defer;

final class AsDefer implements ShouldInterpreter
{
    use ShareableInterpreterConstructor;

    public function handle(Container $container): mixed
    {
        $dependencies = $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);

        defer(fn () => $dependencies->resolve($this->closure, $this->arguments));

        return true;
    }
}
