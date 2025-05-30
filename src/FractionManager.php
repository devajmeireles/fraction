<?php

declare(strict_types=1);

namespace Fraction;

use Closure;
use Fraction\Exceptions\ActionNotRegistered;
use Fraction\Exceptions\UnallowedActionDuplication;
use Fraction\Support\FractionName;
use Illuminate\Foundation\Application;
use UnitEnum;

final class FractionManager
{
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
    public function get(string|UnitEnum $action): mixed
    {
        $original = $action;

        $action = FractionName::format($action);

        return $this->fractions[$action] ?? throw new ActionNotRegistered($original);
    }

    /**
     * Bootstrap the actions.
     */
    public function boot(): void
    {
        $cached = [];

        if ($this->application->isProduction() && file_exists($path = base_path('bootstrap/cache/actions.php'))) {
            $files = require $path;

            foreach ($files as $file) {
                require_once $file;
            }
        } else {
            $files = glob(config('fraction.path').'/*.php');

            foreach ($files as $file) {
                $content = file_get_contents($file);

                if (mb_strpos($content, 'namespace') !== false || mb_strpos($content, 'execute') === false) {
                    continue;
                }

                $cached[] = $file;

                require_once $file;
            }

            if ($cached !== []) {
                file_put_contents(
                    base_path('bootstrap/cache/actions.php'),
                    '<?php return '.var_export($cached, true).';'
                );
            }
        }
    }
}
