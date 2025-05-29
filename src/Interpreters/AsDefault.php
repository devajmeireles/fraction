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
        return $this->dependencies($container)->resolve($this->closure, $this->arguments);
    }

    public function hooks(array $before, array $after): void
    {
        $this->before = $before;

        $this->after = $after;
    }
}
