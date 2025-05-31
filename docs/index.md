---
title: About
---

# About

There's no denying that the "Action Pattern" in the Laravel ecosystem is extremely useful and widely used. However, action classes require "too much content" to do basic things. Let's review a basic action class:

```php
<?php

namespace App\Actions;

use App\Models\User;

class CreateUser
{
    public function handle(array $data): User
    {
        return User::create($data);
    }
}
```

We have a namespace, a class, a method, a return type, a model import, an array as arguments... all of this to create a user. It's overkill for such a simple task, isn't it? For this reason, the _Fraction for Laravel_ solution is revolutionary in the context of Actions. _Fraction for Laravel_ allows you to write actions in a simpler and more direct way, without the need for all this structure, **similar to what PestPHP proposes.**

See what the same example would look like with _Fraction for Laravel_:

```php
<?php

// app/Actions/CreateUser.php

execute('create user', function (array $data) {
    return User::create($data);
});
```

Then, anywhere of your application, you can run this action like this:

```php
<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class CreateUserController extends Controller
{
    public function store(Request $request)
    {
        // ...

        $user = run('create user', $request->all());
    }
}
```

With _Fraction for Laravel_ you will focus on simplifying action creation while maintaining code clarity and readability and **focusing on what really matters: the action logic.** No classes, no namespaces, no fluff, **just what matters: the action logic.**
