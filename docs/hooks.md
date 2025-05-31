---
title: Action Hooks
---

# Concept

Similar ao sistema de Eventos e Listeners do Laravel, o sistema de hooks do _Fraction for Laravel_ permite que você execute ações em sequência, também conhecido similarmente ao conceito de um _pipeline_.

Para funcionar adequadamente, você deve registrar suas ações utilizando o método `then()` e especificando o nome da ação a ser executada em sequência.

```php
<?php

use Illuminate\Http\Request;

execute('create user', function (Request $request) {
    // ...
})->then('send welcome email');

execute('send welcome email', function (Request $request) {
    // ...
});
```

> Uma ação não pode chamar a si próprio em sequência.

## Registering in Different Places

Não importa se você registrar as ações em um só arquivo ou em arquivos diferentes, o `then` será capaz de encontrar e executar a ação.

## Shared Parameters

Como você pode observar no exemplo acima, a instância de `Illuminate\Http\Request` se repete entre ambas as ações, `create user` e `send welcome email`. Isso acontece porque todos os parametros enviados para uma ação é repassada as outras usando o sistema de hooks.

## Undetected Loop

Por padrão o único loop detectado pelo _Fraction for Laravel_ é a tentativa de fazer com que o `then` chame a própria função que o desparou. Nada impede que você crie um efeito `ping` `pong`, isso fica totalmente a seu critério, considerando que você pode criar um loop infinito com isso.
