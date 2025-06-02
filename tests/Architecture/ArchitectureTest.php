<?php

declare(strict_types=1);

use Fraction\Concerns\UsingDefer;
use Fraction\Concerns\UsingLogged;
use Fraction\Concerns\UsingQueue;
use Fraction\Concerns\UsingRescue;
use Fraction\Concerns\UsingThen;
use Fraction\Console\MakeActionCommand;
use Fraction\Contracts\ShouldInterpreter;
use Fraction\Exceptions\ActionNotRegistered;
use Fraction\Exceptions\DependencyUnresolvable;
use Fraction\Exceptions\PreventLoop;
use Fraction\Exceptions\UnallowedActionDuplication;
use Fraction\Facades\Fraction;
use Fraction\FractionBuilder;
use Fraction\FractionManager;
use Fraction\FractionServiceProvider;
use Fraction\Handlers\AsDefer;
use Fraction\Handlers\AsQueue;
use Fraction\Handlers\AsSync;
use Fraction\Handlers\Concerns\ShareableInterpreter;
use Fraction\Jobs\FractionJob;
use Fraction\Support\Bootable;
use Fraction\Support\DependencyResolver;
use Fraction\Support\FractionName;
use Fraction\ValueObjects\Then;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

test('should not use dangerous functions in PHP files')
    ->expect(['dd', 'dump', 'exit', 'ray', 'var_dump'])
    ->not
    ->toBeUsed();

arch()
    ->expect([
        UsingDefer::class,
        UsingQueue::class,
        UsingThen::class,
        UsingRescue::class,
        UsingLogged::class,
    ])
    ->toOnlyBeUsedIn(FractionBuilder::class);

arch()
    ->expect(UsingDefer::class)
    ->toHaveMethod('deferred');

arch()
    ->expect(UsingQueue::class)
    ->toHaveMethod('queued');

arch()
    ->expect(UsingThen::class)
    ->toHaveMethod('then');

arch()
    ->expect(UsingRescue::class)
    ->toHaveMethod('rescued');

arch()
    ->expect(UsingLogged::class)
    ->toHaveMethod('logged');

arch()
    ->expect(ShareableInterpreter::class)
    ->toOnlyBeUsedIn([
        AsSync::class,
        AsQueue::class,
        AsDefer::class,
    ])
    ->toHaveMethods([
        'dependencies',
        'then',
        'hooks',
    ]);

arch()
    ->expect(MakeActionCommand::class)
    ->toExtend(GeneratorCommand::class)
    ->toHaveMethods([
        'getStub',
        'getPath',
        'buildClass',
        'promptForMissingArgumentsUsing',
        'getArguments',
    ]);

arch()
    ->expect(ShouldInterpreter::class)
    ->toHaveMethods([
        'handle',
        'then',
    ]);

arch()
    ->expect([
        PreventLoop::class,
        ActionNotRegistered::class,
        DependencyUnresolvable::class,
        UnallowedActionDuplication::class,
    ])
    ->toHaveConstructor()
    ->toBeFinal()
    ->toExtend(Exception::class);

arch()
    ->expect(Fraction::class)
    ->toExtend(Facade::class)
    ->toHaveMethod('getFacadeAccessor');

arch()
    ->expect([
        AsSync::class,
        AsDefer::class,
        AsQueue::class,
    ])
    ->toImplement(ShouldInterpreter::class)
    ->toUse(ShareableInterpreter::class)
    ->toHaveConstructor()
    ->toHaveMethods([
        'dependencies',
        'then',
        'hooks',
    ]);

arch()
    ->expect(FractionJob::class)
    ->toImplement(ShouldQueue::class)
    ->toUse(Queueable::class)
    ->toHaveConstructor()
    ->toHaveMethod('handle');

test('stub is valid', function () {
    $content = file_get_contents(__DIR__.'/../../src/stubs/action.stub');

    $original = <<<'TXT'
    <?php
    
    execute('{{ name }}', function () {
        // ...
    });

    TXT;

    expect($content)->toBe($original);
});

arch()
    ->expect(Bootable::class)
    ->toBeFinal()
    ->toOnlyBeUsedIn(FractionManager::class)
    ->toHaveMethods([
        'files',
        'files',
    ]);

arch()
    ->expect(DependencyResolver::class)
    ->toBeFinal()
    ->toHaveConstructor()
    ->toHaveMethod('resolve');

arch()
    ->expect(FractionName::class)
    ->toBeFinal()
    ->toHaveMethod('format');

arch()
    ->expect(Then::class)
    ->toHaveConstructor()
    ->toBeFinal();

arch()
    ->expect(FractionBuilder::class)
    ->toBeFinal()
    ->toHaveConstructor()
    ->toBeInvokable()
    ->toHaveMethods([
        '__invoke',
        'then',
        'queued',
        'deferred',
    ]);

arch()
    ->expect(FractionManager::class)
    ->toBeFinal()
    ->toHaveConstructor()
    ->toHaveMethods([
        'register',
        'get',
        'boot',
    ]);

arch()
    ->expect(FractionServiceProvider::class)
    ->toExtend(ServiceProvider::class)
    ->toHaveMethods([
        'register',
        'boot',
    ]);
