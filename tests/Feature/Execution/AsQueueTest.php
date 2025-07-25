<?php

declare(strict_types=1);

use Fraction\Jobs\FractionJob;
use Illuminate\Support\Facades\Queue;

beforeEach(fn () => __delete());

afterAll(fn () => __delete());

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

    expect($builder->toArray()['queued'])->toBeNull();
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

test('ignoring queue', function () {
    execute('one', function () {
        __output('cancelled');
    })->queued();

    run('one', queued: false);

    expect(__exists('cancelled'))->toBeTrue();
});
