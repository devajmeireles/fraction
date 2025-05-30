<?php

declare(strict_types=1);

namespace Fraction\Exceptions;

use Exception;

final class PreventDeferQueueSameTime extends Exception
{
    public function __construct(string $action)
    {
        parent::__construct("The action [$action] cannot use defer and queue at the same time.");
    }
}
