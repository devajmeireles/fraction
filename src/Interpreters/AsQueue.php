<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Support\DependencyResolver;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;

final class AsQueue implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $action,
        protected array $arguments,
        protected SerializableClosure $closure,
    ) {
        // ...
    }

    /** @throws ReflectionException|BindingResolutionException */
    public function handle(Container $application): void
    {
        $application->make(DependencyResolver::class, [
            'action' => $this->action,
        ])->resolve($this->closure, $this->arguments);
    }
}
