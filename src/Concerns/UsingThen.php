<?php

declare(strict_types=1);

namespace Fraction\Concerns;

use Fraction\ValueObjects\Then;
use UnitEnum;

trait UsingThen
{
    /**
     * The array of "then" hooks.
     *
     * @var array<int, string>
     */
    private ?array $then = [];

    /**
     * Register a "then" hook.
     */
    public function then(string|UnitEnum $action): self
    {
        $this->then[] = new Then($this->action, $action);

        return $this;
    }
}
