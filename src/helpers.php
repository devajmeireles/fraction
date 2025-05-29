<?php

use Fraction\Facades\Fraction;
use Fraction\FractionBuilder;

if (!function_exists('fraction')) {
    function fraction(string $action, \Closure $closure): FractionBuilder
    {
        return Fraction::register($action, $closure);
    }
}

if (!function_exists('execute')) {
    function execute(string $action, ...$args): mixed
    {
        /** @var FractionBuilder $builder */
        $builder = Fraction::get($action);

        return $builder($args);
    }
}
