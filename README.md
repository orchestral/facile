Facile Component for Orchestra Platform
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![Build Status](https://travis-ci.org/orchestral/facile.svg?branch=3.8)](https://travis-ci.org/orchestral/facile)
[![Latest Stable Version](https://poser.pugx.org/orchestra/facile/version)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://poser.pugx.org/orchestra/facile/downloads)](https://packagist.org/packages/orchestra/facile)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/facile/v/unstable)](//packagist.org/packages/orchestra/facile)
[![License](https://poser.pugx.org/orchestra/facile/license)](https://packagist.org/packages/orchestra/facile)
[![Coverage Status](https://coveralls.io/repos/github/orchestral/facile/badge.svg?branch=3.8)](https://coveralls.io/github/orchestral/facile?branch=3.8)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Changelog](https://github.com/orchestral/facile/releases)

## Version Compatibility

Laravel    | Facile
:----------|:----------
 5.5.x     | 3.5.x
 5.6.x     | 3.6.x
 5.7.x     | 3.7.x
 5.8.x     | 3.8.x

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "orchestra/facile": "^3.5"
    }
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/facile=^3.5"

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

