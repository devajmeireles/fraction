<?php

declare(strict_types=1);

namespace Fraction\Contracts;

interface Configurable
{
    public function configure(array $data): void;
}
