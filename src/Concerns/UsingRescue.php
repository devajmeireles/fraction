<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Fraction\Configurable\RescuedUsing;

trait UsingRescue
{
    /**
     * Indicates if the action should be rescued.
     */
    private ?RescuedUsing $rescued = null;

    /**
     * Enable the action to be rescued.
     *
     * @return $this
     */
    public function rescued(mixed $default = null): self
    {
        $this->rescued = new RescuedUsing($default);

        return $this;
    }
}
