<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Exceptions\RequiresLaravelTwelve;
use Illuminate\Container\Container;

final class AsDefer implements ShouldInterpreter
{
    use ShareableInterpreter;

    /**
     * @throws RequiresLaravelTwelve
     */
    public function handle(Container $container): true
    {
        if (! function_exists('Illuminate\Support\defer')) {
            throw new RequiresLaravelTwelve();
        }

        $dependencies = $this->dependencies($container);

        \Illuminate\Support\defer(fn () => $dependencies->resolve($this->closure, $this->arguments));

        $this->hooks();

        return true;
    }
}
