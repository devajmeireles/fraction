<?php

declare(strict_types=1);

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

    expect($builder->toArray()['queued'])->toBeNull();
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
