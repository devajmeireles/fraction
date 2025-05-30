<?php

declare(strict_types=1);

namespace Fraction\Configurable;

use Illuminate\Contracts\Support\Arrayable;

final readonly class DeferUsing implements Arrayable
{
    public function __construct(
        public ?string $name = null,
        public ?bool $always = false,
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'always' => $this->always,
        ];
    }
}
