<?php

namespace Fraction;

use Closure;
use Illuminate\Foundation\Application;
use RuntimeException;

class FractionManager
{
    public array $fractions = [];

    public string|array $path;

    public function __construct(public Application $application)
    {
        $this->path = [
            base_path('app/Actions'),
        ];
    }

    public function register(string $action, Closure $closure): FractionBuilder
    {
        if (isset($this->fractions[$action])) {
            throw new RuntimeException("Action '$action' is already registered.");
        }

        $builder = new FractionBuilder($action, $closure);

        $this->fractions[$action] = $builder;

        return $builder;
    }

    public function get(string $action): mixed
    {
        return $this->fractions[$action] ?? throw new RuntimeException("Action '$action' is not registered.");
    }

    public function mount(string|array $path): self
    {
        if (is_string($path)) {
            $path = [$path];
        }

        $this->path = array_merge($this->path, $path);

        return $this;
    }

    public function boot()
    {
        // ...
    }
}
