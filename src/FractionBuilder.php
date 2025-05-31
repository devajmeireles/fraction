<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Configurable\DeferUsing;
use Fraction\Configurable\QueueUsing;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Exceptions\PreventDeferQueueSameTime;
use Fraction\Handlers\AsDefer;
use Fraction\Handlers\AsQueue;
use Fraction\Handlers\AsSync;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use UnitEnum;

final class FractionBuilder implements Arrayable
{
    use Concerns\Builder\UsingDefer;
    use Concerns\Builder\UsingQueue;
    use Concerns\Builder\UsingRescue;
    use Concerns\Builder\UsingThen;

    public function __construct(
        public Application $application,
        public string|UnitEnum $action,
        public Closure $closure
    ) {
        $this->action = $this->action instanceof UnitEnum
            ? $this->action->name
            : $this->action;
    }

    /**
     * Run the action.
     *
     * @throws BindingResolutionException|InvalidArgumentException|PreventDeferQueueSameTime
     */
    public function __invoke(...$arguments): mixed
    {
        if ($this->queued !== null && $this->deferred !== null) {
            throw new PreventDeferQueueSameTime($this->action);
        }

        $interpret = match (true) {
            $this->queued instanceof QueueUsing   => AsQueue::class,
            $this->deferred instanceof DeferUsing => AsDefer::class,
            default                               => AsSync::class,
        };

        /** @var ShouldInterpreter $interpreter */
        $interpreter = $this->application->make($interpret, [
            'action'    => $this->action,
            'arguments' => $arguments,
            'closure'   => new SerializableClosure($this->closure),
        ]);

        $instance = $interpreter->then($this->then);

        if ($this->queued || $this->deferred || $this->rescued) {
            // @phpstan-ignore-next-line
            $interpreter->configure($this->queued?->toArray() ?? $this->deferred?->toArray() ?? $this->rescued?->toArray());
        }

        $result = $instance->handle($this->application);

        if ($this->queued || $this->deferred) {
            return true;
        }

        return $result;
    }

    /** {@inheritDoc} */
    public function toArray(): array
    {
        return [
            'action'   => $this->action,
            'closure'  => $this->closure,
            'then'     => $this->then,
            'queued'   => $this->queued,
            'deferred' => $this->deferred,
            'rescued'  => $this->rescued,
        ];
    }
}
