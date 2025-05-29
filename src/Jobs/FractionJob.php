<?php

declare(strict_types=1);

namespace Fraction\Jobs;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionFunction;

class FractionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $action,
        protected array $arguments,
        protected SerializableClosure $closure,
    ) {
        // ...
    }

    public function handle(Container $application): void
    {
        $reflection = new ReflectionFunction($this->closure->getClosure());

        $parameters = $reflection->getParameters();
        $resolved   = [];

        foreach ($parameters as $index => $parameter) {
            if (array_key_exists($index, $this->arguments)) {
                $resolved[] = $this->arguments[$index];

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

                $resolved[] = $instance->resolve($instance, $application);
            }

            $type = $parameter->getType();

            if ($type && ! $type->isBuiltin()) {
                $resolved[] = $application->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Cannot resolve parameter \${$parameter->getName()} in [{$this->action}]");
            }
        }

        call_user_func_array($this->closure, $resolved);
    }
}
