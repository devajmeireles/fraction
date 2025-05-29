<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

class UnallowedThenForItself extends Exception
{
    public function __construct()
    {
        parent::__construct('The "then" cannot be used to call itself.');
    }
}
