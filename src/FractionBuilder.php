<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Interpreters\AsDefault;
use Fraction\Interpreters\AsDefer;
use Fraction\Interpreters\AsQueue;
use Fraction\Support\FractionName;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use RuntimeException;
use UnitEnum;

final class FractionBuilder
{
    public array $then = [];

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

        $result = $interpreter->then($this->then)->handle($this->application);

        if ($this->queued || $this->deferred) {
            return true;
        }

        return $result;
    }

    public function then(string|UnitEnum $action): self
    {
        if ($this->action === FractionName::format($action)) {
            throw new RuntimeException("Cannot set after action to itself: {$this->action}");
        }

        $this->then[] = $action;

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
