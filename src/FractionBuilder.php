<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Configurable\DeferUsing;
use Fraction\Configurable\QueueUsing;
use Fraction\Contracts\Configurable;
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
    use Concerns\UsingDefer;
    use Concerns\UsingLogged;
    use Concerns\UsingQueue;
    use Concerns\UsingRescue;
    use Concerns\UsingThen;

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

        /** @var ShouldInterpreter|Configurable $interpreter */
        $interpreter = $this->application->make($interpret, [
            'action'    => $this->action,
            'arguments' => $arguments,
            'closure'   => new SerializableClosure($this->closure),
        ]);

        if ([$has, $configuration] = $this->configuration()) {
            if ($has) {
                $interpreter->configure($configuration);
            }
        }

        $result = $interpreter->then($this->then)->handle($this->application);

        if ($this->logged !== null) {
            $this->application->make('log')
                ->channel($this->logged->channel)
                ->info(__($this->logged->message, [
                    'name'   => config('app.name'),
                    'action' => $this->action,
                ]));
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

    /**
     * Determines which configurable should be applied.
     */
    private function configuration(): array
    {
        foreach ([
            $this->queued,
            $this->deferred,
            $this->rescued,
        ] as $instance) {
            if ($instance !== null) {
                return [true, $instance->toArray()];
            }
        }

        return [false, []];
    }
}
