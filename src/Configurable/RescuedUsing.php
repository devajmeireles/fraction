<?php

declare(strict_types=1);

namespace Fraction\Configurable;

use Illuminate\Contracts\Support\Arrayable;

final readonly class RescuedUsing implements Arrayable
{
    public function __construct(
        public mixed $default = null,
    ) {
        //
    }

    /** {@inheritDoc} */
    public function toArray(): array
    {
        return [
            'default' => $this->default,
        ];
    }
}
