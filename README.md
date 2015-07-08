Facile Component for Orchestra Platform
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![MIT License](https://img.shields.io/packagist/l/orchestra/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![Build Status](https://img.shields.io/travis/orchestral/facile/master.svg?style=flat)](https://travis-ci.org/orchestral/facile)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/facile/master.svg?style=flat)](https://coveralls.io/r/orchestral/facile?branch=master)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/facile/master.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/facile/)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)

## Version Compatibility

Laravel    | Facile
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x@dev

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/facile": "~3.0"
	}
}
```

And then run `composer install` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "orchestra/facile=~3.0"

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

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/facile)
* [Change Log](http://orchestraplatform.com/docs/latest/components/facile/changes#v3-1)
