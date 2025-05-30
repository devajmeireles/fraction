<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Fraction\Support\DependencyResolver;
use Fraction\ValueObjects\Then;
use Illuminate\Container\Container;
use Laravel\SerializableClosure\SerializableClosure;

trait ShareableInterpreter
{
    public array $then = [];

    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
    ) {
        // ...
    }

    /**
     * Resolve the dependencies for the action.
     */
    final public function dependencies(Container $container): DependencyResolver
    {
        return $container->make(DependencyResolver::class, [
            'action'      => $this->action,
            'application' => $container,
        ]);
    }

    /** {@inheritDoc} */
    final public function then(array $then): self
    {
        $this->then = $then;

        return $this;
    }

    /**
     * Run the hooks after the action is handled.
     */
    final public function hooks(): void
    {
        if ($this->then === []) {
            return;
        }

        /** @var Then $hook */
        foreach ($this->then as $hook) {
            run($hook->then, ...$this->arguments);
        }
    }
}
