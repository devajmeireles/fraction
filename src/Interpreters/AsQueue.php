<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreterConstructor;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Jobs\FractionJob;
use Illuminate\Container\Container;

final class AsQueue implements ShouldInterpreter
{
    use ShareableInterpreterConstructor;

    public function handle(Container $container): mixed
    {
        return FractionJob::dispatch($this->action, $this->arguments, $this->closure);
    }
}
