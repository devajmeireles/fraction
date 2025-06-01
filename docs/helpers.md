---
title: Helpers
---

If you've made it this far, you probably want to know some of the superpowers that _Fraction_ has, right? From here on, things get more interesting, in terms of features. Before diving into the reading, keep in mind that everything you'll see here is directly associated with the Laravel Way, but without resorting to nonsense.

First, _"WHY do we need helpers?"_ The answer is simple: **to make your life easier**. _Fraction_ provides a set of helpers that allow you to create and manage actions in a more straightforward way. Let's explore some of these helpers.

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
[2025-05-31 21:04:10] local.INFO: [<app.name>] Action: [<action name>] executed at 2025-05-31 21:04:10 
```

Keep in mind the log is written right after the process is dispatched, which means the log output does not represent the exact moment the action logic was executed. For situations where you are interacting with `deferred` or `queued` actions, you might see a difference between the log time and the actual execution time of the action logic, due to the way these actions are processed.
