---
title: File Mapping
---

Since _Fraction_ actions are executed by functions, to speed up performance, a specific directory is mapped, by default: `App/Actions`. However, you can change this directory by publishing the configuration file:

```bash
php artisan vendor:publish --tag=fraction-config
```
