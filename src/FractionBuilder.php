<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Facades\Fraction;
use Fraction\Jobs\FractionJob;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;
use ReflectionFunction;
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

    public function __invoke(...$arguments): mixed
    {
        foreach ($this->before as $before) {
            $before = Fraction::get($before);

            $before();
        }

        $result = null;

        /** @throws ReflectionException|BindingResolutionException|InvalidArgumentException */
        $call = function () use ($arguments): mixed {
            $reflection = new ReflectionFunction($this->closure);

            $parameters = $reflection->getParameters();
            $resolved   = [];

            foreach ($parameters as $index => $parameter) {
                if (array_key_exists($index, $arguments)) {
                    $resolved[] = $arguments[$index];

                    continue;
                }

                foreach ($parameter->getAttributes() as $attribute) {
                    if (! str_contains($attribute->getName(), 'Illuminate\Container\Attributes')) {
                        continue;
                    }

                    $instance = $attribute->newInstance();

                    if (! method_exists($instance, 'resolve')) {
                        continue;
                    }

                    $resolved[] = $instance->resolve($instance, $this->application);
                }

                $type = $parameter->getType();

                if ($type && ! $type->isBuiltin()) {
                    $resolved[] = $this->application->make($type->getName());
                } elseif ($parameter->isDefaultValueAvailable()) {
                    $resolved[] = $parameter->getDefaultValue();
                } else {
                    throw new InvalidArgumentException("Cannot resolve parameter \${$parameter->getName()} in [{$this->action}]");
                }
            }

            return call_user_func_array($this->closure, $resolved);
        };

        if ($this->queued) {
            dispatch(new FractionJob($this->action, $arguments, new SerializableClosure($this->closure)));
        } elseif ($this->deferred) {
            \Illuminate\Support\defer($call);
        } else {
            $result = $call();

            foreach ($this->after as $after) {
                $after = Fraction::get($after);

                $after();
            }
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
