<?php

declare(strict_types=1);

use Fraction\Jobs\FractionJob;
use Illuminate\Support\Facades\Queue;

beforeEach(fn () => __delete());

test('basic', function () {
    Queue::fake();

    execute('testing', function () {
        return true;
    })->queued();

    run('testing');

    Queue::assertPushed(FractionJob::class);
});

test('not defer', function () {
    $builder = execute('testing', function () {
        return 1;
    })->deferred();

    expect($builder->queued)->toBeFalse();
});

test('call then', function () {
    Queue::fake();

    execute('one', function () {
        return 1;
    })->queued()
        ->then('two');

    execute('two', function () {
        __output('two');
    });

    run('one');

    Queue::assertPushed(FractionJob::class);
});

test('call then without fake', function () {
    execute('one', function () {
        return 1;
    })->queued()
        ->then('two');

    execute('two', function () {
        __output('two');
    });

    run('one');

    expect(__exists('two'))->toBeTrue();
});
