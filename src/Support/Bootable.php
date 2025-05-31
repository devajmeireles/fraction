<?php

declare(strict_types=1);

namespace Fraction\Support;

use Illuminate\Foundation\Application;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
final readonly class Bootable
{
    public function __construct(private Application $application)
    {
        //
    }

    /**
     * Boot the fraction.
     */
    public static function fire(Application $application): void
    {
        $class = new self($application);

        $files = $class->files();

        $class->require($files);
    }

    /**
     * Get the files to be booted.
     */
    private function files(): mixed
    {
        if ($this->application->isProduction() && file_exists($path = base_path('bootstrap/cache/actions.php'))) {
            return require $path;
        }

        return glob(config('fraction.path').'/*.php');
    }

    /**
     * Require the files and cache them.
     */
    private function require(array $files): void
    {
        $cached = [];

        foreach ($files as $file) {
            $cached[] = $file;

            require_once $file;
        }

        if ($this->application->isProduction() && $cached !== []) {
            file_put_contents(
                base_path('bootstrap/cache/actions.php'),
                '<?php return '.var_export($cached, true).';'
            );
        }
    }
}
