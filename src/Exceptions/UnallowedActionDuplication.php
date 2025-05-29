<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

final class UnallowedActionDuplication extends Exception
{
    public function __construct(string $action)
    {
        parent::__construct("The action [{$action}] is already registered.");
    }
}
