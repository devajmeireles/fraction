<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Contracts\ShouldInterpreter;
use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Concurrency;
use Laravel\SerializableClosure\SerializableClosure;

final class AsDefer implements ShouldInterpreter
{
    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
    ) {
        //
    }

    public function handle(Container $container): mixed
    {
        $dependencies = $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);

        Concurrency::defer(fn () => $dependencies->resolve($this->closure, $this->arguments));

        return true;
    }
}
