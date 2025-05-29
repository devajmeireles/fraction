<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreterConstructor;
use Fraction\Contracts\ShouldInterpreter;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;

final class AsDefault implements ShouldInterpreter
{
    use ShareableInterpreterConstructor;

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function handle(Container $container): mixed
    {
        return $this->dependencies($container)->resolve($this->closure, $this->arguments);
    }
}
