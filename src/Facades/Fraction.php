<?php

declare(strict_types=1);

namespace Fraction\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Fraction\FractionBuilder register(string|\UnitEnum $action, \Closure $closure)
 * @method static \Fraction\FractionBuilder get(string|\UnitEnum $action)
 * @method static array all()
 * @method static void boot()
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
