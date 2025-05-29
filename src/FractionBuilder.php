<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Facades\Fraction;
use Fraction\Jobs\FractionJob;
use Fraction\Support\DependencyResolver;
use Illuminate\Foundation\Application;
use Laravel\SerializableClosure\SerializableClosure;
use RuntimeException;

use function Illuminate\Support\defer;

final class FractionBuilder
{
    public array $before = [];

    public array $after = [];

    public bool $queued = false;

    public bool $deferred = false;

    public function __construct(
        public Application $application,
        public string $action,
        public Closure $closure
    ) {
        // ...
    }

    /** @throws @throws ReflectionException|BindingResolutionException|InvalidArgumentException */
    public function __invoke(...$arguments): mixed
    {
        foreach ($this->before as $before) {
            $before = Fraction::get($before);

            $before();
        }

        if ($this->queued) {
            dispatch(new FractionJob($this->action, $arguments, new SerializableClosure($this->closure)));

            return true;
        }

        $dependencies = $this->application->make(DependencyResolver::class, [
            'action' => $this->action,
        ]);

        $resolve = $dependencies->resolve(...);

        if ($this->deferred) {
            defer(fn () => $resolve($this->closure, $arguments));

            return true;
        }

        $result = $resolve($this->closure, $arguments);

        foreach ($this->after as $after) {
            $after = Fraction::get($after);

            $after();
        }

        return $result;
    }

    public function before(string $action): self
    {
        if ($this->action === $action) {
            throw new RuntimeException("Cannot set before action to itself: {$this->action}");
        }

        $this->before[] = $action;

        return $this;
    }

    public function after(string $action): self
    {
        if ($this->action === $action) {
            throw new RuntimeException("Cannot set after action to itself: {$this->action}");
        }

        $this->after[] = $action;

        return $this;
    }

    public function queued(bool $queued = true): self
    {
        $this->queued = $queued;

        return $this;
    }

    public function deferred(bool $deferred = true): self
    {
        $this->deferred = $deferred;

        return $this;
    }
}
