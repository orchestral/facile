Orchestra Platform Facile Component
==============
 
Orchestra\Facile simplify the need to create API based response in your Laravel 4 application.

[![Build Status](https://travis-ci.org/orchestral/facile.png?branch=master)](https://travis-ci.org/orchestral/facile)

## Quick Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
	"require": {
		"orchestra/facile": "dev-master"
	},
	"minimum-stability": "dev"
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

* [Documentation](http://docs.orchestraplatform.com/pages/components/facile)
* [Change Logs](https://github.com/orchestral/facile/wiki/Change-Logs)
