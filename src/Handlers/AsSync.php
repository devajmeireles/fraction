<?php

declare(strict_types=1);

namespace Fraction\Handlers;

use Fraction\Configurable\RescuedUsing;
use Fraction\Contracts\Configurable;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Handlers\Concerns\ShareableInterpreter;
use Illuminate\Container\Container;

final class AsSync implements Configurable, ShouldInterpreter
{
    use ShareableInterpreter;

    /**
     * Execute the action rescued.
     */
    public ?RescuedUsing $rescued = null;

    public function handle(Container $container): mixed
    {
        $resolve = $this->dependencies($container)->resolve(...);

        $result = match (true) {
            $this->rescued !== null => rescue(fn () => $resolve($this->closure, $this->arguments), $this->rescued->default),
            default                 => $resolve($this->closure, $this->arguments),
        };

        $this->hooks();

        return $result;
    }

    public function configure(array $data): void
    {
        $this->rescued = new RescuedUsing(...$data);
    }
}
