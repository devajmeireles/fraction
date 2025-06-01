---
title: Testing
---

There is nothing "special" about _Fraction_ regarding tests. This means that you can call your actions using `run` inside tests as normal. For example, if you have an action that `creates new user`, you can test it like this:

```php
// app/Actions/CreateUser.php

use App\Models\User;

execute('create new user', function (array $data) {
    return User::create($data);
});
```

```php {2}
test('should be able to create new user', function () {
    $user = run('create new user', [
        'name' => $name = fake()->name(),
        'email' => $email = fake()->email(),
        'password' => bcrypt('password'),
    ]);
    
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($name)
        ->and($user->email)->toBe($email);
});
```

## Handle Unregistered Actions

Since _Fraction_ is all about fundamentally defining and using actions based on string names, you might accidentally make mistakes that throw exceptions — when an action in use has not been registered an exception `\Fraction\Exceptions\ActionNotRegistered` will be thrown. 

To prevent this, we provide the `actions:unregistered` Artisan command that lists all actions in use in the `app/` namespace and lists the action and the file it was detected in. **This way, you can include this command in a basic test to make sure that everything is ok with defining and using string-based actions**:

```php {2}
test('ensure all actions name are correctly applied', function () {
    $output = Artisan::call('actions:unregistered');

    expect($output)->toBe(0);
});
```

If this test fails, it means that there are actions in your code that are not registered. You can use the command to see which actions are not registered and where they are used in your codebase.

```bash
php artisan actions:unregistered
```
