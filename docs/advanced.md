---
title: Advanced
---

# Actions Inside Actions

Você é totalmente livre para executar ações dentro de ações, o _Fraction for Laravel_ não limita isso.

```php
<?php

execute('one', function () {
    execute('two', function () {
        return 2;
    });

    return run('two');
});

$result = run('one'); // 2
```
