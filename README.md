Facile Component for Orchestra Platform
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![Build Status](https://travis-ci.org/orchestral/extension.svg?branch=master)](https://travis-ci.org/orchestral/extension)
[![Latest Stable Version](https://poser.pugx.org/orchestra/facile/version)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://poser.pugx.org/orchestra/facile/downloads)](https://packagist.org/packages/orchestra/facile)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/facile/v/unstable)](//packagist.org/packages/orchestra/facile)
[![License](https://poser.pugx.org/orchestra/facile/license)](https://packagist.org/packages/orchestra/facile)

## Table of Content

* [Version Compatibility](#version-compatibility)
* [Installation](#installation)
* [Configuration](#configuration)
* [Changelog](https://github.com/orchestral/facile/releases)

## Version Compatibility

Laravel    | Facile
:----------|:----------
 4.0.x     | 2.0.x
 4.1.x     | 2.1.x
 4.2.x     | 2.2.x
 5.0.x     | 3.0.x
 5.1.x     | 3.1.x
 5.2.x     | 3.2.x
 5.3.x     | 3.3.x
 5.4.x     | 3.4.x
 5.5.x     | 3.5.x@dev

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

