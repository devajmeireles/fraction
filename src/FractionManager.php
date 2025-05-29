<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Support\FractionName;
use Illuminate\Foundation\Application;
use RuntimeException;
use UnitEnum;

class FractionManager
{
    public array $fractions = [];

    public function __construct(public Application $application)
    {
        //
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

    public function boot(): void
    {
        $files = glob(base_path('app/Actions').'/*.php');

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if (mb_strpos($content, 'execute(') === false) {
                continue;
            }

            require_once $file;
        }
    }
}
