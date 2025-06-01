---
title: About
---

# Welcome to _Fraction_ 🎯

There's no denying that the "Action Pattern" in the Laravel ecosystem is extremely useful and widely used. However, action classes require "too much content" to do basic things. Let's review a basic action class:

```php {9}
namespace App\Actions;

use App\Models\User;

class CreateUser
{
    public function handle(array $data): User
    {
        return User::create($data); // [!code focus]
    }
}
```

We have a namespace, a class, a method, a return type, a model import, an array as arguments... **all of this to create a user.** It's overkill for such a simple task, isn't it? 

For this reason, the _Fraction_ solution is revolutionary in the context of Actions. _Fraction_ allows you to write actions in a simpler and more direct way, without the need for all this structure, **similar to what _PestPHP_ proposes.**

See what the same example would look like with _Fraction_:

```php {3}
// app/Actions/Users.php

execute('create user', function (array $data) {
    return User::create($data);
});
```

Then, anywhere in your application, you can `run` this action like this:

```php {9}
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateUserController extends Controller
{
    public function store(Request $request)
    {
        $user = run('create user', $request->all()); // [!code focus]
        
        // ...
    }
}
```

With _Fraction_, you will focus on simplifying action creation while maintaining code clarity and readability and **focusing on what really matters: the action logic.** No classes, no namespaces, no fluff, **just what matters: the action logic** 🎯
