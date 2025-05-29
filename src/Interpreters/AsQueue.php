<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Jobs\FractionJob;
use Illuminate\Container\Container;
use Illuminate\Foundation\Bus\PendingDispatch;

final class AsQueue implements ShouldInterpreter
{
    use ShareableInterpreter;

    public function handle(Container $container): PendingDispatch
    {
        return FractionJob::dispatch(
            $this->action,
            $this->arguments,
            $this->closure,
            $this->then
        );
    }
}
