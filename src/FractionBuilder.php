<?php

namespace Fraction;

use Fraction\Facades\Fraction;
use RuntimeException;

class FractionBuilder
{
    public array $before = [];

    public array $after = [];

    public function __construct(public string $action, public \Closure $closure)
    {
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

    public function __invoke(...$args): mixed
    {
        foreach ($this->before as $before) {
            $before = Fraction::get($before);

            $before();
        }

        $result = app()->wrap($this->closure)(...$args);

        foreach ($this->after as $after) {
            $after = Fraction::get($after);

            $after();
        }

        return $result;
    }
}
