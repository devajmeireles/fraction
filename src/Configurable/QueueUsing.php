<?php

declare(strict_types=1);

namespace Fraction\Configurable;

use Illuminate\Contracts\Support\Arrayable;

final readonly class QueueUsing implements Arrayable
{
    public function __construct(
        public mixed $delay = null,
        public ?string $queue = null,
        public ?string $connection = null,
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'delay'      => $this->delay,
            'queue'      => $this->queue,
            'connection' => $this->connection,
        ];
    }
}
