<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreterConstructor;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;

final class AsDefault implements ShouldInterpreter
{
    use ShareableInterpreterConstructor;

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function handle(Container $container): mixed
    {
        $dependencies = $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);

        return $dependencies->resolve($this->closure, $this->arguments);
    }
}
