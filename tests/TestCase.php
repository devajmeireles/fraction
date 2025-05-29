<?php

declare(strict_types=1);

namespace Tests;

use Fraction\Facades\Fraction;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithViews;
    use WithWorkbench;

    protected function getPackageAliases($app): array
    {
        return [
            'Fraction' => Fraction::class,
        ];
    }
}
