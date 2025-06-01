---
title: Using
---

While you can create actions manually, there is a `make:action` command that can be used to make it easier to create actions via the terminal. The output of the command is like this:

```bash
php artisan make:action CreateUser
```
Will result in an action like this:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
});
```

As you may have noticed in the example above, running the command `php artisan make:action CreateUser` would create the action file `CreateUser.php` inside `app/Actions`. While you can use this model, it's a good idea to try to isolate your actions into files whose names are logically associated with your application's "domains" such as `app/Actions/Users.php`, `app/Actions/Emails.php`, etc. This will help you keep your code organized and maintainable.

### Running Actions

To run an action, you can use the `run` function in anywhere in your application:

```php
$user = run('create user');
```

### Action Arguments

As you might expect, you can pass any type of arguments to actions:

```php
// app/Actions/CreateUser.php

use App\Models\User;

execute('create user', function (array $data) {
    return User::create($data);
});
```

The execution of the action in this case would be like this:

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request\CreateUserRequest;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        // ...

        $user = run('create user', $request->validated());
    }
}
```

And yes, you can return anything from an action. The returned value will be received when executing the `run` function, as is normally the case with a function call.

## Problem & Solution

One of the problems that was initially noticed when _Fraction_ was created was that we interacted with strings. This is bad because if we forget a single letter, creating or executing the action can become a problem. For this reason you have two easy solutions - [one we will mention now and the other we will mention in the testing section](/testing#handle-unregistered-actions). Your first option is use `UnitEnum` to define your actions through cases, which can be useful to avoid writing errors.

```php
namespace App\Enums;

enum UserActions
{
    case CreateUser;
}
```

```php
// app/Actions/CreateUser.php

use App\Enums\UserActions;

execute(UserActions::CreateUser, function () {
    // ...
});
```

When choosing to use `UnitEnum` - _which I also prefer_, both the declaration and the execution of the action must be done using the case associated with the enum:

```php
use App\Enums\UserActions;

// ...

run(UserActions::CreateUser);
```

## Dependency Injection

Do you remember all the work we had to do in the past to get actions wrapped in the Laravel container by default, before using _Fraction_? That's one of the problems _Fraction_ solves: **everything related to _Fraction_ actions is encapsulated in the Laravel container by default.** <ins>Since actions are fully resolved by the Laravel container</ins>, you can rely on Laravel's dependency resolution to inject any necessary dependencies into the action. 

For example, if you want to inject an instance of `Illuminate\Http\Request`:

```php
// app/Actions/CreateUser.php

use Illuminate\Http\Request;

execute('create user', function (Request $request) {
    // ...
});
```

Additionally, _Fraction_ can also resolve the new container's attribute:

```php
// app/Actions/SendWelcomeEmail.php

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

execute('send welcome email', function (#[CurrentUser] User $user) {
    // ...
});
```
