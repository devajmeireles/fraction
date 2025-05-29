<?php

declare(strict_types=1);

namespace Fraction\Contracts;

use Illuminate\Container\Container;

interface ShouldInterpreter
{
    public function handle(Container $container): mixed;

    public function then(array $then): self;
}
