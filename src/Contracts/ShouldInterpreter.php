<?php

declare(strict_types=1);

namespace Fraction\Contracts;

use Illuminate\Container\Container;

interface ShouldInterpreter
{
    /**
     * Handle the action.
     */
    public function handle(Container $container): mixed;

    /**
     * Register the action's hooks.
     */
    public function then(array $then): self;
}
