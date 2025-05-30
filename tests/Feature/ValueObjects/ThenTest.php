<?php

declare(strict_types=1);

use Fraction\Exceptions\PreventLoop;
use Fraction\ValueObjects\Then;

test('can instantiate', function () {
    $then = new Then('foo', 'foo');

    expect($then->then)->toBe('foo');
});

test('cannot instantiate due same name', function () {
    $then = new Then('__fraction.foo', 'foo');

    expect($then->then)->toBe('foo');
})->throws(PreventLoop::class, 'The hook "then" cannot be used to invoke itself.');
