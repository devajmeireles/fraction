<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Fraction\Configurable\LoggedUsing;

trait UsingLogged
{
    /**
     * Configuration for logging the action.
     */
    private ?LoggedUsing $logged = null;

    /**
     * Enable the action to be deferred.
     *
     * @return $this
     */
    public function logged(?string $channel = null): self
    {
        $this->logged = new LoggedUsing($channel);

        return $this;
    }
}
