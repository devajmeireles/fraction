<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

/** @codeCoverageIgnore */
final class DependencyUnresolvable extends Exception
{
    public function __construct(string $name, string $action)
    {
        parent::__construct("The dependency [{$name}] cannot be resolved for the action [{$action}].");
    }
}
