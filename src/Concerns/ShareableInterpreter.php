<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;
use Laravel\SerializableClosure\SerializableClosure;

trait ShareableInterpreter
{
    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
        public array $before = [],
        public array $after = [],
    ) {
        // ...
    }

    final public function dependencies(Container $container): mixed
    {
        return $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);
    }
}
