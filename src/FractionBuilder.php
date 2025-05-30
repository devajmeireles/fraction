<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Configurable\DeferUsing;
use Fraction\Configurable\QueueUsing;
use Fraction\Contracts\Configurable;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Exceptions\PreventDeferQueueSameTime;
use Fraction\Interpreters\AsDefault;
use Fraction\Interpreters\AsDefer;
use Fraction\Interpreters\AsQueue;
use Fraction\ValueObjects\Then;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use InvalidArgumentException;
use Laravel\SerializableClosure\SerializableClosure;
use UnitEnum;

final class FractionBuilder
{
    public array $then = [];

    public ?QueueUsing $queued = null;

    public ?DeferUsing $deferred = null;

    public function __construct(
        public Application $application,
        public string $action,
        public Closure $closure
    ) {
        // ...
    }

    /** @throws BindingResolutionException|InvalidArgumentException|PreventDeferQueueSameTime
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

        if ($interpreter instanceof Configurable) {
            $interpreter->configure($this->queued?->toArray() ?? $this->deferred->toArray());
        }

        $result = $instance->handle($this->application);

        if ($this->queued || $this->deferred) {
            return true;
        }

        return $result;
    }

    public function then(string|UnitEnum $action): self
    {
        $this->then[] = new Then($this->action, $action);

        return $this;
    }

    public function queued(
        mixed $delay = null,
        ?string $queue = null,
        ?string $connection = null,
    ): self {
        $this->queued = new QueueUsing($delay, $queue, $connection);

        return $this;
    }

    public function deferred(
        bool $always = false,
        ?string $name = null,
    ): self {
        $this->deferred = new DeferUsing($name, $always);

        return $this;
    }
}
