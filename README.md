# Welcome to _Fraction_ 🎯

There's no denying that the "Action Pattern" in the Laravel ecosystem is extremely useful and widely used. However, action classes require "too much content" to do basic things. Let's review a basic action class:

```php
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

We have a 1) Namespace, 2) Class, 3) Method... **All of this to create a user?** It's overkill for such a simple task, isn't it?

For this reason, the _Fraction_ solution is revolutionary in the context of Actions. _Fraction_ allows you to write actions in a _simpler and more direct way_, without the need for all this structure, **similar to what _PestPHP_ proposes.**

---

## Documentation

Now that I've made you curious about what _Fraction_ does 😉 [check out the official website by clicking here](https://fractionforlaravel.com/). There you'll find a complete explanation of the package, as well as understanding what it really offers along with its many benefits.
