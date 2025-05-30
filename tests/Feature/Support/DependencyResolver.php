<?php

declare(strict_types=1);

use Fraction\Exceptions\DependencyUnresolvable;
use Fraction\Support\DependencyResolver;

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

test('cannot serialize and resolve dependencies', function () {
    $function = function ($foo) {
        return $foo;
    };

    $dependency = new DependencyResolver('foo', $this->app);

    expect($dependency->resolve($function))->toBe('bar');
})->throws(DependencyUnresolvable::class, 'The dependency [foo] cannot be resolved for the action [foo]');
