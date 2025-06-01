---
title: Deployment
---

Since actions are not classes, but files that is registered via `required_once`. In production, _Fraction_ will automatically create a cache inside the `bootstrap/cache/actions.php` file to improve the performance of action registration.

You should run the following command during deployment:

```bash
php artisan optimize:clear
```
