<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Contracts\ShouldInterpreter;
use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use ReflectionException;

final class AsDefault implements ShouldInterpreter
{
    use ShareableInterpreter;

    /**
     * @throws ReflectionException|BindingResolutionException
     */
    public function handle(Container $container): mixed
    {
        $result = $this->dependencies($container)->resolve($this->closure, $this->arguments);

        $this->hooks();

        return $result;
    }
}
