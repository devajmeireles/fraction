<?php

declare(strict_types=1);

use Fraction\Exceptions\DependencyUnresolvable;
use Fraction\Support\DependencyResolver;
use Illuminate\Database\Eloquent\Model;

test('can serialize and resolve dependencies', function () {
    $function = function (string $foo = 'bar') {
        return $foo;
    };

    $dependency = new DependencyResolver('foo', $this->app);

    expect($dependency->resolve($function))->toBe('bar');
});

test('can serialize using SerializableClosure and resolve dependencies', function () {
    $function = new Laravel\SerializableClosure\SerializableClosure(function (string $foo = 'bar') {
        return $foo;
    });

    $dependency = new DependencyResolver('foo', $this->app);

    expect($dependency->resolve($function))->toBe('bar');
});

test('can resolve multiples types', function () {
    execute('one', function (array|Model|null $foo = null) {
        return $foo;
    })->rescued();

    $test = run('one');

    expect($test)->toBeNull();
});

test('cannot serialize and resolve dependencies', function () {
    $function = function ($foo) {
        return $foo;
    };

    $dependency = new DependencyResolver('foo', $this->app);

    expect($dependency->resolve($function))->toBe('bar');
})->throws(DependencyUnresolvable::class, 'The dependency [foo] cannot be resolved for the action [foo]');
