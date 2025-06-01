---
title: Hooks
---

Similar to the Laravel's Event and Listener system, the _Fraction_ hook system allows you to execute actions in sequence, much like the concept of a _pipeline_. For this, you should call the `then` method after registering an action. 

Here is an example of a full _pipeline_ for:

    1. Creating a user
    2. Sending welcome email
    3. Enabling free trial of a product
    4. Send account ready notification

We start by defining the actions that will _create the user_, without any `then` hook:

```php
// app/Actions/Users.php

execute('create user', function (array $data) {
    return User::create($data);
});
```

Then, we define the action that will `send welcome email`. Here we set the `then` hook to call the next action in the pipeline, which is `enabling free trial`:

```php {7}
// app/Actions/Emails.php

use App\Models\User;

execute('send welcome user email', function (User $user) {
    // ...
})->then('enable free trial');
```

After that, we define the action that will `enable free trial`, and for this action, we set another `then` hook to call the next action in the pipeline, which is `send account ready notification`:

```php {5}
// app/Actions/Products.php

use Illuminate\Http\Request;

execute('enable free trial', function (User $user) {
    // ...
})->then('send account ready notification');
```

Finally, we define the action that will perform the last step of your _pipeline, `send a user account ready notification`. Since this is the last action in the pipeline, we do not set any `then` hook:

```php {5}
// app/Actions/Notifications.php

use Illuminate\Http\Request;

execute('send account ready notification', function (User $user) {
    // ...
});
```

Now you _should_ `run` the first action of the pipeline: `send welcome user email`.

```php
namespace App\Http\Controllers;
use Illuminate\Http\Request\CreateUserRequest;

class UserController extends Controller
{
    public function store(CreateUserRequest $request)
    {
        // ...

        $user = run('create user', $request->validated());

        run('send welcome user email', $user);
    }
}
```

Although the above example uses generic names, you can choose to use a name that makes it easier for you to understand, such as: `new user pipeline`. Also, we only used _"sync actions"_ in the above scenario, but you are completely free to use the `then` hook on actions of type `deferred` or `queued`.

## Shared Arguments

You may have noticed that from the `send welcome user email` action onwards the `\App\Models\User $user` argument is repeated in the other actions, right? This happens because all the arguments sent to an action are passed to the others using the hook `then` system.

## Undetected Loop

By default, the only loop _Fraction_ detected is the attempt to call the _then_ hook for the same function that defines it. Nothing is stopping you from creating a ping-pong effect; that's up to you, but make no mistake: you can create an infinite loop with this, **so be careful!**
