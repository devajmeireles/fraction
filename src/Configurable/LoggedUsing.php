<?php

declare(strict_types=1);

namespace Fraction\Configurable;

use Illuminate\Contracts\Support\Arrayable;

final class LoggedUsing implements Arrayable
{
    public function __construct(
        public ?string $channel = null,
        public ?string $message = null,
    ) {
        $this->message = $message ?? __('[:name] Action: [:action] executed.');
    }

    /** {@inheritDoc} */
    public function toArray(): array
    {
        return [
            'channel' => $this->channel,
            'message' => $this->message,
        ];
    }
}
