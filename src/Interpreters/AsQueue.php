<?php

declare(strict_types=1);

namespace Fraction\Interpreters;

use Fraction\Concerns\ShareableInterpreter;
use Fraction\Configurable\QueueUsing;
use Fraction\Contracts\Configurable;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Jobs\FractionJob;
use Illuminate\Container\Container;
use Illuminate\Foundation\Bus\PendingDispatch;

final class AsQueue implements Configurable, ShouldInterpreter
{
    use ShareableInterpreter;

    public QueueUsing $queue;

    public function handle(Container $container): PendingDispatch
    {
        return FractionJob::dispatch(
            $this->action,
            $this->arguments,
            $this->closure,
            $this->then
        )
            ->delay($this->queue->delay)
            ->onQueue($this->queue->queue)
            ->onConnection($this->queue->connection);
    }

    public function configure(array $data): void
    {
        $this->queue = new QueueUsing(...$data);
    }
}
