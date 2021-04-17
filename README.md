Facile Component for Orchestra Platform
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![tests](https://github.com/orchestral/facile/actions/workflows/tests.yml/badge.svg?branch=master)](https://github.com/orchestral/facile/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/orchestra/facile/version)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://poser.pugx.org/orchestra/facile/downloads)](https://packagist.org/packages/orchestra/facile)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/facile/v/unstable)](//packagist.org/packages/orchestra/facile)
[![License](https://poser.pugx.org/orchestra/facile/license)](https://packagist.org/packages/orchestra/facile)
[![Coverage Status](https://coveralls.io/repos/github/orchestral/facile/badge.svg?branch=master)](https://coveralls.io/github/orchestral/facile?branch=master)

## Version Compatibility

Laravel    | Facile
:----------|:----------
 5.5.x     | 3.5.x
 5.6.x     | 3.6.x
 5.7.x     | 3.7.x
 5.8.x     | 3.8.x
 6.x       | 4.x
 7.x       | 5.x

## Installation

To install through composer, run the following command from terminal:

```bash
composer require "orchestra/facile"
```

## Configuration

Next add the service provider in `config/app.php`.

```php
'providers' => [

    // ...

    Orchestra\Facile\FacileServiceProvider::class,
],
```

You might want to add `Orchestra\Support\Facades\Facile` to class aliases in `config/app.php`:

```php
'aliases' => [

    // ...

    'Facile' => Orchestra\Support\Facades\Facile::class,
],
```

