<?php

declare(strict_types=1);

namespace Fraction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Fraction\FractionManager mount(string|array $path)
 * @method static void boot()
 * @method static \Fraction\FractionBuilder register(string $action, \Closure $closure)
 * @method static \Closure get(string $action)
 *
 * @see \Fraction\FractionManager
 */
class Fraction extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'fraction';
    }
}
