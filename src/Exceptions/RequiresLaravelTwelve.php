<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

/** @codeCoverageIgnore */
final class RequiresLaravelTwelve extends Exception
{
    public function __construct()
    {
        parent::__construct('This feature requires Laravel 12 or higher.');
    }
}
