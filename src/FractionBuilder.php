<?php

namespace Fraction;

use Closure;
use Fraction\Facades\Fraction;
use Illuminate\Foundation\Application;
use RuntimeException;

class FractionBuilder
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

    public function __invoke(...$args): mixed
    {
        foreach ($this->before as $before) {
            $before = Fraction::get($before);

            $before();
        }

        $closure = $this->application->wrap($this->closure);

        $result = null;

        if ($this->queued) {
            dispatch(fn () => $closure(...$args));
        } elseif ($this->deferred) {
            \Illuminate\Support\defer(fn () => $closure(...$args));
        } else {
            $result = $closure(...$args);

            foreach ($this->after as $after) {
                $after = Fraction::get($after);

                $after();
            }
        }

        return $result;
    }
}
