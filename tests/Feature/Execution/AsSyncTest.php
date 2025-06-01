<?php

declare(strict_types=1);

use Fraction\Exceptions\ActionNotRegistered;
use Fraction\Exceptions\PreventDeferQueueSameTime;
use Fraction\Exceptions\UnallowedActionDuplication;
use Fraction\ValueObjects\Then;
use Illuminate\Cache\Repository;
use Illuminate\Container\Attributes\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Facades\Log;
use TiMacDonald\Log\LogEntry;
use TiMacDonald\Log\LogFake;

beforeEach(fn () => __delete());

afterAll(fn () => __delete());

test('basic', function (mixed $data) {
    execute('testing', function (mixed $data) {
        return $data;
    });

    $test = run('testing', $data);

    expect($test)->toBe($data);

})->with([
    'test',
    'test2',
    1,
    10,
    [1, 2, 3],
]);

test('can execute using enum', function () {
    execute(Actions::Testing, function () {
        return 1;
    });

    $test = run(Actions::Testing);

    expect($test)->toBe(1);
});

test('can execute action inside action', function () {
    execute('one', function () {
        execute('two', function () {
            return 2;
        });

        return run('two');
    });

    $test = run('one');

    expect($test)->toBe(2);
});

test('resolve laravel dependencies', function () {
    execute('testing', function (Request $request) {
        return $request->method();
    });

    $test = run('testing');

    expect($test)->toBe('GET');

});

test('resolve laravel attribute dependencies', function () {
    CacheFacade::put('foo', 'bar', 10);

    execute('testing', function (#[Cache] Repository $cache) {
        return $cache->get('foo');
    });

    $test = run('testing');

    expect($test)->toBe('bar');

});

test('resolve auto dependencies', function () {
    execute('testing', function (string $foo = 'foo') {
        return $foo;
    });

    $test = run('testing');

    expect($test)->toBe('foo');

});

test('not queue', function () {
    $builder = execute('testing', function () {
        return 1;
    });

    expect($builder->toArray()['queued'])->toBeNull();
});

test('not deferred', function () {
    $builder = execute('testing', function () {
        return 1;
    });

    expect($builder->toArray()['deferred'])->toBeNull();
});

test('call then', function () {
    execute('one', function () {
        return 1;
    })->then('two');

    execute('two', function () {
        __output('two');
    });

    $test = run('one');

    expect($test)
        ->toBe(1)
        ->and(__exists('two'))
        ->toBeTrue();
});

test('call rescued', function () {
    execute('one', function () {
        throw new Exception('foo');
    })->rescued();

    $test = run('one');

    expect($test)->toBeNull();
});

test('call logged', function () {
    LogFake::bind();

    $this->travelTo(now()->createFromTimeString('2025-05-31 21:17:06'));

    execute('one', function () {
        return 1;
    })->logged();

    $test = run('one');

    expect($test)->toBe(1);

    Log::assertLogged(fn (LogEntry $log) => $log->level === 'info'
        && $log->message === '[Laravel] Action: [one] executed.'
    );
});

test('call rescued using default value', function () {
    execute('one', function () {
        throw new Exception('foo');
    })->rescued('bar');

    $test = run('one');

    expect($test)->toBe('bar');
});

test('call then sharing data', function () {
    CacheFacade::put('foo', 'foo-bar-baz-bah', 10);

    execute('testing', function (#[Cache] Repository $cache) {
        return $cache->get('foo');
    })->then('two');

    execute('two', function (#[Cache] Repository $cache) {
        __output($cache->get('foo'));
    });

    $test = run('testing');

    expect($test)
        ->toBe('foo-bar-baz-bah')
        ->and(__exists('foo-bar-baz-bah'))
        ->toBeTrue();
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
});

test('ensure then order', function () {
    $builder = execute('one', function () {
        return 1;
    })
        ->then('two')
        ->then('tree')
        ->then('four');

    $array = $builder->toArray();

    expect($array['then'])
        ->toHaveCount(3)
        ->and($array['then'][0])
        ->toBeInstanceOf(Then::class)
        ->and($array['then'][0]->then)
        ->toBe('two')
        ->and($array['then'][1])
        ->toBeInstanceOf(Then::class)
        ->and($array['then'][1]->then)
        ->toBe('tree')
        ->and($array['then'][2])
        ->toBeInstanceOf(Then::class)
        ->and($array['then'][2]->then)
        ->toBe('four');
});

test('cannot use a non-existent action', function () {
    execute('foo', function () {
        return 'foo';
    });

    $test = run('bar');

    expect($test)->toBe('foo');
})->throws(ActionNotRegistered::class, 'The action [bar] is not registered.');

test('cannot register twice', function () {
    execute('foo', function () {
        return 'foo';
    });

    execute('foo', function () {
        return 'foo';
    });

    $test = run('foo');

    expect($test)->toBe('foo');
})->throws(UnallowedActionDuplication::class, 'The action [foo] is already registered.');

test('cannot set queued and deferred at same time', function () {
    execute('testing', function () {
        return 'foo';
    })->queued()
        ->deferred();

    run('testing');
})->throws(PreventDeferQueueSameTime::class, 'The action [testing] cannot use defer and queue at the same time.');

enum Actions
{
    case Testing;
}
