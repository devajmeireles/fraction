<?php

declare(strict_types=1);

use Fraction\ValueObjects\Then;

beforeEach(fn () => __delete());

afterAll(fn () => __delete());

test('basic', function () {
    execute('testing', function () {
        return __output();
    })->deferred();

    expect(run('testing'))->toBeTrue();
});

test('not queue', function () {
    $builder = execute('testing', function () {
        return 1;
    })->deferred();

    expect($builder->queued)->toBeNull();
});

test('call then', function () {
    execute('one', function () {
        __output('one');
    })->deferred()
        ->then('two');

    execute('two', function () {
        __output('two');
    })->deferred();

    $test = run('one');

    expect($test)->toBeTrue();
});

test('call then sequentially', function () {
    execute('one', function () {
        return 1;
    })
        ->then('two')
        ->then('tree')
        ->then('four');

    execute('two', function () {
        __output('two');
    });

    execute('tree', function () {
        __output('tree');
    });

    execute('four', function () {
        __output('four');
    });

    $test = run('one');

    expect($test)
        ->toBe(1)
        ->and(__exists('two'))
        ->toBeTrue()
        ->and(__exists('tree'))
        ->toBeTrue()
        ->and(__exists('four'))
        ->toBeTrue();
})->skip('Implement when DeferUsing is ready');

test('ensure then order', function () {
    $builder = execute('one', function () {
        return 1;
    })
        ->deferred()
        ->then('two')
        ->then('tree')
        ->then('four');

    expect($builder->deferred())
        ->not()
        ->toBeNull()
        ->and($builder->then)
        ->toHaveCount(3)
        ->and($builder->then[0])
        ->toBeInstanceOf(Then::class)
        ->and($builder->then[0]->then)
        ->toBe('two')
        ->and($builder->then[1])
        ->toBeInstanceOf(Then::class)
        ->and($builder->then[1]->then)
        ->toBe('tree')
        ->and($builder->then[2])
        ->toBeInstanceOf(Then::class)
        ->and($builder->then[2]->then)
        ->toBe('four');
});
