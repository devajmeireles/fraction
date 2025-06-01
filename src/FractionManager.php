<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Exceptions\ActionNotRegistered;
use Fraction\Exceptions\UnallowedActionDuplication;
use Fraction\Support\Bootable;
use Fraction\Support\FractionName;
use Illuminate\Foundation\Application;
use UnitEnum;

final class FractionManager
{
    /**
     * The array of registered fractions.
     *
     * @var array<string, FractionBuilder>
     */
    private array $fractions = [];

    public function __construct(public Application $application)
    {
        //
    }

    /**
     * Register a new action.
     *
     * @throws UnallowedActionDuplication
     */
    public function register(string|UnitEnum $action, Closure $closure): FractionBuilder
    {
        $original = $action;

        $action = FractionName::format($action);

        if (isset($this->fractions[$action])) {
            throw new UnallowedActionDuplication($original);
        }

        $builder = new FractionBuilder($this->application, $original, $closure);

        $this->fractions[$action] = $builder;

        return $builder;
    }

    /**
     * Get the action by its name.
     *
     * @throws ActionNotRegistered
     */
    public function get(string|UnitEnum $action): FractionBuilder
    {
        $original = $action;

        $action = FractionName::format($action);

        return $this->fractions[$action] ?? throw new ActionNotRegistered($original);
    }

    /**
     * Get all registered actions.
     */
    public function all(): array
    {
        return $this->fractions;
    }

    /**
     * Bootstrap the actions.
     */
    public function boot(): void
    {
        Bootable::fire($this->application);
    }
}
