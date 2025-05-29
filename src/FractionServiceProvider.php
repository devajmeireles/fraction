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
        $this->app->singleton('fraction', fn (Application $app) => new FractionManager($app));
    }

    public function boot(): void
    {
        Fraction::boot();
    }
}
