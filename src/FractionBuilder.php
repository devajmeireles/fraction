<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Configurable\DeferUsing;
use Fraction\Configurable\QueueUsing;
use Fraction\Configurable\RescuedUsing;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Exceptions\PreventDeferQueueSameTime;
use Fraction\Interpreters\AsDefault;
use Fraction\Interpreters\AsDefer;
use Fraction\Interpreters\AsQueue;
use Fraction\ValueObjects\Then;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use UnitEnum;

final class FractionBuilder implements Arrayable
{
    /**
     * The array of "then" hooks.
     *
     * @var array<int, string>
     */
    private array $then = [];

    /**
     * Configuration for queueing the action.
     */
    private ?QueueUsing $queued = null;

    /**
     * Configuration for deferring the action.
     */
    private ?DeferUsing $deferred = null;

    /**
     * Indicates if the action should be rescued.
     */
    private ?RescuedUsing $rescued = null;

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
            default                               => AsDefault::class,
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

    /**
     * Register a "then" hook.
     */
    public function then(string|UnitEnum $action): self
    {
        $this->then[] = new Then($this->action, $action);

        return $this;
    }

    /**
     * Enable the action to be queued.
     *
     * @return $this
     */
    public function queued(
        mixed $delay = null,
        ?string $queue = null,
        ?string $connection = null,
    ): self {
        $this->queued = new QueueUsing($delay, $queue, $connection);

        return $this;
    }

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
