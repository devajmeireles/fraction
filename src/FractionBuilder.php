<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Interpreters\AsDefault;
use Fraction\Interpreters\AsDefer;
use Fraction\Interpreters\AsQueue;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use RuntimeException;

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

    /** @throws BindingResolutionException|InvalidArgumentException */
    public function __invoke(...$arguments): mixed
    {
        $interpret = match (true) {
            $this->queued   => AsQueue::class,
            $this->deferred => AsDefer::class,
            default         => AsDefault::class,
        };

        /** @var ShouldInterpreter $interpreter */
        $interpreter = $this->application->make($interpret, [
            'action'    => $this->action,
            'arguments' => $arguments,
            'closure'   => new SerializableClosure($this->closure),
        ]);

        $result = $interpreter->handle($this->application);

        if ($this->queued || $this->deferred) {
            return true;
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
