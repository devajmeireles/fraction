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
     * @param  string|null  $channel  - `null` to use the default logging channel.
     * @return $this
     */
    public function logged(?string $channel = null): self
    {
        $this->logged = new LoggedUsing($channel ?? config('logging.default'));

        return $this;
    }
}
