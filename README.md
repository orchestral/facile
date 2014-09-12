Orchestra Platform Facile Component
==============

Facile Component simplify the need to create API based response in your Laravel application.

[![Latest Stable Version](https://poser.pugx.org/orchestra/facile/v/stable.png)](https://packagist.org/packages/orchestra/facile)
[![Total Downloads](https://poser.pugx.org/orchestra/facile/downloads.png)](https://packagist.org/packages/orchestra/facile)
[![Build Status](https://travis-ci.org/orchestral/facile.svg?branch=master)](https://travis-ci.org/orchestral/facile)
[![Coverage Status](https://coveralls.io/repos/orchestral/facile/badge.png?branch=master)](https://coveralls.io/r/orchestral/facile?branch=master)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/orchestral/facile/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/orchestral/facile/)

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
'providers' => array(

	// ...

	'Orchestra\Facile\FacileServiceProvider',
),
```

You might want to add `Orchestra\Support\Facades\Facile` to class aliases in `app/config/app.php`:

```php
'aliases' => array(

	// ...

	'Facile' => 'Orchestra\Support\Facades\Facile',
),
```

## Resources

* [Documentation](http://orchestraplatform.com/docs/latest/components/facile)
* [Change Log](http://orchestraplatform.com/docs/latest/components/facile/changes#v3-0)
