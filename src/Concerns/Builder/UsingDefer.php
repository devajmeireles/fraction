<?php

declare(strict_types=1);

namespace Fraction\Concerns\Builder;

use Fraction\Configurable\DeferUsing;

trait UsingDefer
{
    /**
     * Configuration for deferring the action.
     */
    private ?DeferUsing $deferred = null;

    /**
     * Enable the action to be deferred.
     *
     * @return $this
     */
    public function deferred(
        bool $always = false,
        ?string $name = null,
    ): self {
        $this->deferred = new DeferUsing($name, $always);

        return $this;
    }
}
