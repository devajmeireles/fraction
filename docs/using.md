---
title: Using
---

## File Map

Since _Fraction_ actions are executed by functions - there is no namespace, to speed up performance, a specific directory is mapped, by default: `App/Actions`. However, you can change this directory by publishing the configuration file:

```bash
php artisan vendor:publish --tag=fraction-config
```

> Any functions registered outside this namespace will not be registered.

## Creating Actions

While you can create actions manually, there is a make:action command that can be used to make it easier to create actions via the terminal. The output of the command like this:

```bash
php artisan make:action SendWelcomeEmail
```
Will result in an action like this:

```php
<?php

// app/Actions/SendWelcomeEmail.php

execute('send welcome email', function () {
    // ...
});
```

## Executing Actions

To run an action, you can use the `run` function in anywhere in your application.

```php
<?php

$user = run('send welcome email');
```

Optionally, you can pass arguments to the action.

```php
<?php

use App\Models\User;
use App\Notifications\WelcomeEmailNotification;

execute('send welcome email', function (User $user) {
    $user->notify(new WelcomeEmailNotification());
    
    $user->touch('welcome_email_sent_at');
    
    return $user;
});
```

```php
<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function sendWelcomeEmail(Request $request)
    {
        // ...

        $user = run('send welcome email', $request->user());
    }
}
```

As you may have noticed above, the user object is returned from the `send welcome email` action. You can return anything from an action. The returned value will be received by executing the `run` function.

> The `queued` e `deferred` only returns `true`.

## Support UnitEnum

You can also use `UnitEnum` to define your actions through cases, which can be useful to avoid writing errors.

```php
<?php

namespace App\Enums;

enum UserActions
{
    case SendWelcomeEmail;
}
```

```php
<?php

use App\Enums\UserActions;

execute(UserActions::SendWelcomeEmail, function () {
    // ...
});
```

You should call the action using the enum as well:

```php
<?php

// ...

use App\Enums\UserActions;

run(UserActions::SendWelcomeEmail);
```

## Dependency Injection

Since actions are fully resolved by the Laravel container, you can rely on Laravel's dependency resolution to inject any necessary dependencies into the action. For example, if you want to inject an instance of `Illuminate\Http\Request`:

```php
<?php

use Illuminate\Http\Request;

execute('send welcome email', function (Request $request) {
    // ...
});
```

Obviously, the _Fraction_ can also resolve the new container's attribute:

```php
<?php

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

execute('send welcome email', function (#[CurrentUser] User $user) {
    // ...
});
```

## Deferred Actions

As part of Laravel 11, you can trigger deferred actions simply by using the `deferred` method following the action declaration:

```php
<?php

execute('send welcome email', function () {
    // ...
})->deferred();
```

Behind the scenes, this will register the action as a deferred action, using the `Illuminate\Support\defer` function.

> You can pass parameters to the deferred method to personalize the deferred execution.

## Queued Actions

You can trigger queued actions simply by using the `queued` method following the action declaration:

```php
<?php

execute('send welcome email', function () {
    // ...
})->queued();
```

Behind the scenes, this will register the action to dispatch the `Fraction\Jobs\FractionJob` job, which will execute the action in the background.

> You can pass parameters to the queued method to personalize the queued execution.

## Rescued Actions

You can trigger rescued actions simply by using the `rescued` method following the action declaration:

```php
<?php

execute('send welcome email', function () {
    // ...
})->rescued();
```

Behind the scenes, this will register the action to execute the function inside the `rescue` Laravel's function, which aims to do not stop the execution of the application in case of an error.

You can also pass a default value to the `rescued` method, which will be returned in case of an error:

```php
<?php

execute('send welcome email', function () {
    throw new Exception('ops!');
})->rescued(default: false);
```
