<?php

declare(strict_types=1);

pest()->extend(Tests\TestCase::class)->in('Feature', 'Architecture');

function __output(?string $id = null): string
{
    $id ??= uniqid();

    if (! is_dir(__DIR__.'/fixtures/output')) {
        mkdir(__DIR__.'/fixtures/output', 0755, true);
    }

    file_put_contents(__DIR__.'/fixtures/output/'.$id.'.txt', true);

    return $id;
}

function __exists(string $id): bool
{
    return file_exists(__DIR__.'/fixtures/output/'.$id.'.txt');
}

function __delete(): void
{
    if (is_dir(__DIR__.'/fixtures/output')) {
        $files = glob(__DIR__.'/fixtures/output/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
