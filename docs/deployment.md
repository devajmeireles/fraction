---
title: Deployment
---

# Deployment

Since actions are not classes, action files are registered via `required_once`. In production, the _Fraction_ package will automatically create a cache inside the `bootstrap/cache/actions.php` file to improve the performance of action registration.

You should run the following command during deployment:

```bash
php artisan optimize:clear
```
