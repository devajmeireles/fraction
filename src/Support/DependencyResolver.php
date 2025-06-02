<?php

declare(strict_types=1);

namespace Fraction\Support;

use Closure;
use Fraction\Exceptions\DependencyUnresolvable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Laravel\SerializableClosure\SerializableClosure;
use ReflectionException;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;

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
     * Resolve the dependencies of a closure or serializable closure.
     *
     * @throws ReflectionException|BindingResolutionException|DependencyUnresolvable
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

            /** @var ReflectionParameter|ReflectionNamedType $type */
            $type = $parameter->getType();

            if ($type && (method_exists($type, 'isBuiltin') && ! $type->isBuiltin())) {
                $resolved[] = $this->application->make($type->getName());
            } elseif ($parameter->isDefaultValueAvailable()) {
                $resolved[] = $parameter->getDefaultValue();
            } else {
                throw new DependencyUnresolvable($parameter->getName(), $this->action);
            }
        }

        return call_user_func_array($closure, $resolved);
    }
}
