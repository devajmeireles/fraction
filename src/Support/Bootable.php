<?php

declare(strict_types=1);

namespace Fraction\Support;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
final readonly class Bootable
{
    public function __construct()
    {
        //
    }

    /**
     * Boot the fraction.
     */
    public static function fire(): void
    {
        $class = new self();

        $files = $class->files();

        if ($files === [] || $files === false) {
            return;
        }

        foreach ($files as $file) {
            require $file;
        }
    }

    /**
     * Get the files to be booted.
     */
    private function files(): array|false
    {
        return glob(config('fraction.path').'/*.php');
    }
}
