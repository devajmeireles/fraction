<?php

declare(strict_types=1);

namespace Fraction\Jobs;

use Fraction\Support\DependencyResolver;
use Fraction\ValueObjects\Then;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;

final class FractionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $action,
        protected array $arguments,
        protected SerializableClosure $closure,
        public array $then = [],
    ) {
        // ...
    }

    /** @throws ReflectionException|BindingResolutionException */
    public function handle(Container $application): void
    {
        $application->make(DependencyResolver::class, [
            'action' => $this->action,
        ])->resolve($this->closure, $this->arguments);

        if ($this->then === []) {
            return;
        }

        /** @var Then $hook */
        foreach ($this->then as $hook) {
            run($hook->then, ...$this->arguments);
        }
    }
}
