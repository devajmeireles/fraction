<?php

declare(strict_types=1);

use Fraction\Support\FractionName;

test('can format name', function () {
    $name = FractionName::format('test');

    expect($name)->toBe('__fraction.test');
});

test('cannot use a long name', function () {
    $name = FractionName::format(fake()->text());

    expect($name)->toBe('__fraction.test');
})->throws(InvalidArgumentException::class, 'Fraction name cannot be longer than 50 characters.');
