<?php

declare(strict_types=1);

namespace Fraction\Contracts;

interface Configurable
{
    /**
     * Configure the interpreter with the given data.
     */
    public function configure(array $data): void;
}
