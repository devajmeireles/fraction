<?php

declare(strict_types=1);

namespace Fraction\Facades;

use Illuminate\Support\Facades\Facade;

class Fraction extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'fraction';
    }
}
