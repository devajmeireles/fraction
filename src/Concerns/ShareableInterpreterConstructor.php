<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Laravel\SerializableClosure\SerializableClosure;

trait ShareableInterpreterConstructor
{
    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
    ) {
        // ...
    }
}
