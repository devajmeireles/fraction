<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Contracts\ShouldInterpreter;
use Fraction\Jobs\FractionJob;
use Illuminate\Container\Container;
use Laravel\SerializableClosure\SerializableClosure;

final class AsQueue implements ShouldInterpreter
{
    public function __construct(
        public string $action,
        public array $arguments,
        public SerializableClosure $closure,
    ) {
        //
    }

    public function handle(Container $container): mixed
    {
        return FractionJob::dispatch($this->action, $this->arguments, $this->closure);
    }
}
