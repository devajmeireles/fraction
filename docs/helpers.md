---
title: Using
---

If you're familiar with modern Laravel, you'll know that there are dozens of things in Laravel that are useful in a variety of contexts, all of which are part of the _"Laravel Way"_. That's why _Fraction_ provides you with several useful helpers for creating your actions.

## Deferred Actions

As part of Laravel 11, you can trigger deferred actions simply by using the `deferred` method following the action declaration:

```php
<?php

// app/Actions/Emails.php

execute('send welcome email', function () {
    // ...
})->deferred(name: 'send welcome email', always: true);
```

Behind the scenes, this will register the action as a deferred action, using the `Illuminate\Support\defer` function.

> Different the _sync actions_, `deferred` actions only returns `true` when executed successfully.

## Queued Actions

You can trigger queued actions simply by using the `queued` method following the action declaration:

```php
<?php

// app/Actions/Emails.php

execute('send welcome email', function () {
    // ...
})->queued(delay: 10, queue: 'actions', connection: 'redis');
```

Behind the scenes, this will register the action to dispatch the `Fraction\Jobs\FractionJob` job, which will execute the action in the background.

> Different the _sync actions_, `queued` actions only returns `true` when executed successfully.

## Rescued Actions

You can trigger rescued actions simply by using the `rescued` method following the action declaration:

```php
<?php

// app/Actions/Emails.php

execute('send welcome email', function () {
    // ...
})->rescued();
```

Behind the scenes, this will register the action to execute the function inside the `rescue` Laravel's function, which aims to do not stop the execution of the application in case of an error.

You can also pass a default value to the `rescued` method, which will be returned in case of an error:

```php
<?php

// app/Actions/Emails.php

execute('send welcome email', function () {
    throw new Exception('ops!');
})->rescued(default: false);
```

```php
$result = run('send welcome email'); // false
```

## Logged Actions

You can trigger logged actions simply by using the `logged` method following the action declaration:

```php
<?php

// app/Actions/Emails.php

execute('send welcome email', function () {
    // ...
})->logged(channel: 'stack');
```

Behind the scenes, this will write a log to the requested `channel` to help you understand the exact moment the action was performed. The log output will be written as follows:

```txt
[2025-05-31 21:04:10] local.INFO: [<app.name>] Action: [<action name>] executed at 2025-05-31 21:04:10 
```

Keep in mind the log is written right after the process is dispatched, which means the log output does not represent the exact moment the action logic was executed. For situations where you are interacting with `deferred` or `queued` actions, you might see a difference between the log time and the actual execution time of the action logic, due to the way these actions are processed.
