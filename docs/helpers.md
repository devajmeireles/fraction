---
title: Helpers
---

If you've made it this far, you probably want to know some of the superpowers that _Fraction_ has, right? From here on, things get more interesting, in terms of features. Before diving into the reading, keep in mind that everything you'll see here is directly associated with the Laravel Way, but without resorting to nonsense.

## Deferred Actions

As part of Laravel 11, you can trigger deferred actions simply by using the `deferred` method following the action declaration:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
})->deferred(name: 'createUser', always: true);
```

Behind the scenes, this will register the action as a deferred action, using the `Illuminate\Support\defer` function.

> The `deferred` actions only returns `true`, when executed successfully.

## Queued Actions

You can trigger queued actions simply by using the `queued` method following the action declaration:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
})->queued(delay: 10, queue: 'actions', connection: 'redis');
```

Behind the scenes, this will register the action to dispatch the `Fraction\Jobs\FractionJob` job, which will execute the action in the background.

> The `queued` actions only returns `true`, when executed successfully.

## Rescued Actions

You can trigger rescued actions simply by using the `rescued` method following the action declaration:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
})->rescued();
```

Behind the scenes, this will register the action to execute the function inside the `rescue` Laravel's function, which aims to do not stop the execution of the application in case of an error. You can also pass a default value to the `rescued` method, which will be returned in case of an error:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    throw new Exception('ops!');
})->rescued(default: false);
```

```php
$result = run('create user'); // false
```

## Logged Actions

Do you, like me, sometimes need to know when an action was executed? With that in mind, you can trigger logged actions simply by using the `logged` method following the action declaration:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
})->logged(channel: 'stack');
```

Behind the scenes, this will write a log to the requested `channel` to help you understand the exact moment the action was performed. The log output will be written as follows:

```txt
[2025-05-31 21:04:10] local.INFO: [<app.name>] Action: [<action name>] executed. 
```

Keep in mind the log is written right after the process is dispatched, which means the log output does not represent the exact moment the action logic was executed. For situations where you are interacting with `deferred` or `queued` actions, you might see a difference between the log time and the actual execution time of the action logic, due to the way these actions are processed.

## Ignoring Helpers at Runtime

Now that you've read about helpers, you might be wondering if there's a way to ignore a specific helper to be applied to an action at runtime, am I right? The answer is: yes, you can determine an action as, for example, `deferred`, but ignore the `deferred` at runtime:

You don't need to do anything special in the action itself:

```php
// app/Actions/CreateUser.php

execute('create user', function () {
    // ...
})->deferred();
```

Since you may still need to perform this action as deferred at any time, you need to interact with the `run` function to bypass the `deferred` helper at runtime:

```php {3}
// ...

run('create user', deferred: false); // [!code focus]
```

This way, the action will still be logged as a `deferred` action, but it will not be executed as a deferred action at the runtime you want to avoid. The same applies to the other helpers, such as `queued`, `rescued`, `logged`, and `then`.

Here is the complete list of options you can use to ignore helpers at runtime:

| Term              |                         What |
|-------------------|-----------------------------:|
| `deferred: false` | To ignore `deferred` actions |
| `queued: false`   |   To ignore `queued` actions |
| `rescued: false`  |  To ignore `rescued` actions |
| `logged: false`   |   To ignore `logged` actions |
| `logged: false`   |   To ignore `logged` actions |
| `then: false`     |              To ignore hooks |
