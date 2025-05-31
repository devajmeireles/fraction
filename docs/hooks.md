---
title: Action Hooks
---

# Concept

Similar to the Laravel's Event and Listener system, the _Fraction_ hook system allows you to execute actions in sequence, also similarly to the concept of a _pipeline_. For this, you shoul call the `then` method after registering an action:

```php
<?php

use Illuminate\Http\Request;

// app/Actions/Users.php

execute('create user', function (Request $request) {
    // ...
})->then('send welcome user email');
```

```php
<?php

// app/Actions//Emails.php

execute('send welcome user email', function (Request $request) {
    // ...
})->then('enable free trial');
```

```php
<?php

// app/Actions/Products.php

execute('enable free trial', function (Request $request) {
    // ...
});
```

> An action cannot call itself in a hook.

## Shared Parameters

As you can see in the example above, the `Illuminate\Http\Request` instance is repeated between both the `create user` and `send welcome email` actions. This happens because all the parameters sent to one action are passed to the others using the hook system.

## Undetected Loop

By default, the only loop detected by _Fraction_ is the attempt to make `then` call the function that triggered it. There is nothing stopping you from creating a `ping` `pong` effect, this is completely up to you, considering that you can create an infinite loop with this.
