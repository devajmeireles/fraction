<?php

declare(strict_types=1);

namespace Fraction\Concerns\Builder;

use Fraction\Configurable\QueueUsing;

trait UsingQueue
{
    /**
     * Configuration for queueing the action.
     */
    private ?QueueUsing $queued = null;

    /**
     * Enable the action to be queued.
     *
     * @return $this
     */
    public function queued(
        mixed $delay = null,
        ?string $queue = null,
        ?string $connection = null,
    ): self {
        $this->queued = new QueueUsing($delay, $queue, $connection);

        return $this;
    }
}
