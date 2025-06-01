---
title: Testing
---

There is nothing "special" about _Fraction_ regarding tests. This means that you can call your actions using `run` inside tests as normal. 

For example, if you have an action that `creates new user`: 

```php
// app/Actions/CreateUser.php

use App\Models\User;

execute('create new user', function (array $data) {
    return User::create($data);
});
```

Then test your action like this:

::: code-group

```php {5} [PestPHP]
test('should be able to create new user', function () {
    $name = fake()->name();
    $email = fake()->email();

    $user = run('create new user', [
        'name' => $name,
        'email' => $email,
        'password' => bcrypt('password'),
    ]);
    
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($name)
        ->and($user->email)->toBe($email);
});
```

```php {16} [PhpUnit]
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_be_able_to_create_new_user(): void
    {
        $name = fake()->name();
        $email = fake()->email();

        $user = run('create new user', [
            'name' => $name,
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $this->assertInstanceOf(User::class, $user);

        $this->assertSame($name, $user->name);
        $this->assertSame($email, $user->email);
    }
}
```

:::

## Handle Unregistered Actions

While you can define actions using `UnitEnum` - [as demonstrated on the using](/using#problem-solution) page, fundamentally _Fraction_ is about defining actions based on string names. For this reason, you can accidentally make typos that throw exceptions, because when an action in use has not been registered, a `\Fraction\Exceptions\ActionNotRegistered` exception will be thrown.

To prevent this, we provide the `actions:unregistered` Artisan command that lists all actions in use in the `base_path('app/')` namespace and lists the action and the file it was detected in. This way, you can include this command in a basic test to make sure that everything is ok with defining and using string-based actions:

::: code-group

```php {4} [PestPHP]
use Illuminate\Support\Facades\Artisan;

test('ensure all actions name are correctly applied', function () {
    $output = Artisan::call('actions:unregistered');

    expect($output)->toBe(0);
});
```

```php {13} [PhpUnit]
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnregisteredActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_ensure_all_actions_names_are_correctly_applied(): void
    {
        $output = Artisan::call('actions:unregistered');

        $this->assertSame(0, $output);
    }
}
```

:::

If this test fails - _the `$output` is different from 0_, **it means that there are actions in your code that are not registered.** You can use the command via terminal to see which actions are not registered and where they are used in your codebase.

```bash
php artisan actions:unregistered
```

The output will look like this:

```txt
WARN  Unregistered actions found:

┌──────────────────────────────────┬─────────────────────┐
│ File                             │ Unregistered Action │
├──────────────────────────────────┼─────────────────────┤
│ app/Livewire/Users/Index.php:37  │ create new user     │
└──────────────────────────────────┴─────────────────────┘
```

> [!IMPORTANT]
> Behind the scenes, the `actions:unregistered` command will apply a `grep` command using a regular expression that aims to identify the use of the `run` function. For this reason, the command does not differentiate the usage of `run` functions that are commented.
