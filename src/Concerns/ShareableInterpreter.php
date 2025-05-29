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
        public array $then = [],
    ) {
        // ...
    }

    final public function dependencies(Container $container): DependencyResolver
    {
        return $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);
    }

    public function then(array $then): self
    {
        $this->then = $then;

        return $this;
    }

    final public function hooks(): void
    {
        if ($this->then === []) {
            return;
        }

        foreach ($this->then as $hook) {
            run($hook, ...$this->arguments);
        }
    }
}
