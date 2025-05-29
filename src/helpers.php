<?php

declare(strict_types=1);

use Fraction\Facades\Fraction;
use Fraction\FractionBuilder;

if (! function_exists('execute')) {
    function execute(string $action, Closure $closure): FractionBuilder
    {
        return Fraction::register($action, $closure);
    }
}

if (! function_exists('run')) {
    function run(string $action, ...$args): mixed
    {
        /** @var FractionBuilder $builder */
        $builder = Fraction::get($action);

        return $builder(...$args);
    }
}
