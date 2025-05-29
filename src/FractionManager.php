<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Exceptions\ActionNotRegistered;
use Fraction\Exceptions\UnallowedActionDuplication;
use Fraction\Support\FractionName;
use Illuminate\Foundation\Application;
use UnitEnum;

class FractionManager
{
    public array $fractions = [];

    public function __construct(public Application $application)
    {
        //
    }

    /**
     * @throws UnallowedActionDuplication
     */
    public function register(string|UnitEnum $action, Closure $closure): FractionBuilder
    {
        $original = $action;

        $action = FractionName::format($action);

        if (isset($this->fractions[$action])) {
            throw new UnallowedActionDuplication($action);
        }

        $builder = new FractionBuilder($this->application, $original, $closure);

        $this->fractions[$action] = $builder;

        return $builder;
    }

    /**
     * @throws ActionNotRegistered
     */
    public function get(string|UnitEnum $action): mixed
    {
        $original = $action;

        $action = FractionName::format($action);

        return $this->fractions[$action] ?? throw new ActionNotRegistered($original);
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
