<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Configurable\DeferUsing;
use Fraction\Contracts\Configurable;
use Fraction\Contracts\ShouldInterpreter;
use Illuminate\Container\Container;

final class AsDefer implements Configurable, ShouldInterpreter
{
    use ShareableInterpreter;

    public DeferUsing $defer;

    public function handle(Container $container): true
    {
        $dependencies = $this->dependencies($container);

        \Illuminate\Support\defer(
            fn () => $dependencies->resolve($this->closure, $this->arguments),
            $this->defer->name,
            $this->defer->always,
        );

        $this->hooks();

        return true;
    }

    public function configure(array $data): void
    {
        $this->defer = new DeferUsing(...$data);
    }
}
