<?php

declare(strict_types=1);

namespace Fraction\Support;

use Closure;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;
use ReflectionFunction;

/** @internal */
final readonly class DependencyResolver
{
    public function __construct(
        private string $action,
        private Application $application,
    ) {
        //
    }

    /**
     * //
     *
     * @throws ReflectionException|BindingResolutionException|InvalidArgumentException
     */
    public function resolve(Closure|SerializableClosure $closure, array $arguments = []): mixed
    {
        $closure = $closure instanceof SerializableClosure
            ? $closure->getClosure()
            : $closure;

        $reflection = new ReflectionFunction($closure);

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

        return call_user_func_array($closure, $resolved);
    }
}
