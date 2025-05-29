<?php

declare(strict_types=1);

namespace Fraction;

use Fraction\Facades\Fraction;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FractionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerBindins();

        $this->registerCommands();
    }

    public function boot(): void
    {
        Fraction::boot();
    }

    private function registerBindins(): void
    {
        $this->app->singleton('fraction', fn (Application $app) => new FractionManager($app));
    }

    private function registerCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\MakeActionCommand::class,
        ]);
    }
}
