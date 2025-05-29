<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Contracts\ShouldInterpreter;
use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;

final class AsDefault implements ShouldInterpreter
{
    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
    ) {
        // ...
    }

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
