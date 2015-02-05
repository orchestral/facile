Orchestra Platform Facile Component
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![Latest Stable Version](https://img.shields.io/github/release/orchestral/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://img.shields.io/packagist/dt/orchestra/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![MIT License](https://img.shields.io/packagist/l/orchestra/facile.svg?style=flat)](https://packagist.org/packages/orchestra/facile)
[![Build Status](https://img.shields.io/travis/orchestral/facile/3.0.svg?style=flat)](https://travis-ci.org/orchestral/facile)
[![Coverage Status](https://img.shields.io/coveralls/orchestral/facile/3.0.svg?style=flat)](https://coveralls.io/r/orchestral/facile?branch=3.0)
[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/orchestral/facile/3.0.svg?style=flat)](https://scrutinizer-ci.com/g/orchestral/facile/)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/facile": "3.0.*"
	}
}
```

Next add the service provider in `app/config/app.php`.

```php
'providers' => [

	// ...

	'Orchestra\Facile\FacileServiceProvider',
],
```

You might want to add `Orchestra\Support\Facades\Facile` to class aliases in `app/config/app.php`:

```php
'aliases' => [

	// ...

	'Facile' => 'Orchestra\Support\Facades\Facile',
],
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/facile)
* [Change Log](http://orchestraplatform.com/docs/latest/components/facile/changes#v3-0)
