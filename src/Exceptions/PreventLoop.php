<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

class PreventLoop extends Exception
{
    public function __construct()
    {
        parent::__construct('The hook "then" cannot be used to invoke itself.');
    }
}
