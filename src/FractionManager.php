<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Support\FractionName;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use RuntimeException;
use UnitEnum;

class FractionManager
{
    public array $fractions = [];

    public string|array $path = [];

    public function __construct(public Application $application)
    {
        $this->path = config('fraction.paths');
    }

    public function register(string|UnitEnum $action, Closure $closure): FractionBuilder
    {
        $action = FractionName::format($action);

        if (isset($this->fractions[$action])) {
            throw new RuntimeException("Action '$action' is already registered.");
        }

        $builder = new FractionBuilder($this->application, $action, $closure);

        $this->fractions[$action] = $builder;

        return $builder;
    }

    public function get(string|UnitEnum $action): mixed
    {
        $action = FractionName::format($action);

        return $this->fractions[$action] ?? throw new RuntimeException("Action '$action' is not registered.");
    }

    public function mount(string|array $path): self
    {
        $path = Arr::wrap($path);

        $this->path = array_merge($this->path, $path);

        return $this;
    }

    public function boot(): void
    {
        foreach ($this->path as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $files = glob($path.'/*.php');

            foreach ($files as $file) {
                require_once $file;
            }
        }
    }
}
