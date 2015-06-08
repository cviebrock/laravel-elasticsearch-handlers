# Laravel-Elasticsearch-Handlers

An even easier way to use the official Elastic Search client in your Laravel applications.

[![Build Status](https://travis-ci.org/cviebrock/laravel-elasticsearch-handlers.svg)](https://travis-ci.org/cviebrock/laravel-elasticsearch-handlers)
[![Total Downloads](https://poser.pugx.org/cviebrock/laravel-elasticsearch-handlers/downloads.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch-handlers)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch-handlers/v/stable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch-handlers)
[![Latest Stable Version](https://poser.pugx.org/cviebrock/laravel-elasticsearch-handlers/v/unstable.png)](https://packagist.org/packages/cviebrock/laravel-elasticsearch-handlers)

* [Installation and Configuration](#installation)
* [Usage](#usage)
* [Creating Handlers](#creating-handlers)
  * [Special `boot` Method](#special-boot-method)
* [Pre-Defined Handlers](#pre-defined-handlers)
  * [EnvironmentIndexPrefixHandler](#environment-index-prefix-handler)
  * [IndexTemplateHandler](#index-template-handler)
* [Bugs, Suggestions and Contributions](#bugs)
* [Copyright and License](#copyright)



<a name="installation"></a>
## Installation and Configuration

1. Install the `cviebrock/laravel-elasticsearch-handlers` package via composer:

    ```shell
    $ composer require cviebrock/laravel-elasticsearch-handlers
    ```
    
2. Publish the configuration file.  For Laravel 5:

    ```shell
    php artisan vendor:publish cviebrock/laravel-elasticsearch-handlers
    ```

    In order to make this package also work with Laravel 4, we can't do the
    standard configuration publishing like most Laravel 4 packages do.  You will
    need to simply copy the configuration file into your application's configuration folder:
    
    ```shell
    cp vendor/cviebrock/laravel-elasticsearch-handlers/config/elasticsearch-handlers.php app/config/
    ```

3. Add the service provider (`config/app.php` for Laravel 5 or `app/config/app.php` for Laravel 4).
The service provider needs to come after the `LaravelElasticsearch` provider.

    ```php
    'providers' => array(
        ...
        'Cviebrock\LaravelElasticSearch\ServiceProvider',
        'Cviebrock\LaravelElasticSearchHandlers\ServiceProvider',
    )
    ```
    
    
<a name="usage"></a>
## Usage

This package extends the `laravel-elasticsearch` package by returning a "decorated" 
Elasticsearch client class, instead of the default PHP client.  You can configure
how the client is decorated on a per-connection basis.

Take the following example `elasticsearch-handlers.php` configuration:

```php
<?php

return [
	'connections' => [
    	'default' => [
			'clientClass' => 'Cviebrock\LaravelElasticsearchHandlers\Client',
			'handlers' => []
		]
	]
];
```

When you instantiate an Elasticsearch client with:

```php
$client = Elasticsearch::connection('default');
```

the package will create a base client (using the base `elasticsearch.php`
configuration) then wrap it in the class defined by the _clientClass_ setting,
and inject the _handlerConfig_ array.  So, in this case, an instance of
`Cviebrock\LaravelElasticsearchHandlers\Client` is returned.

This class the bare minimum client wrapper.  It doesn't do anything except
pass-through all commands to the wrapped base Elasticsearch class.

Let's make it more useful ...


<a name="creating-handlers"></a>
## Creating Handlers

Pretend you only have one Elasticsearch instance running, but you need it to
support indices for several of your Laravel application environments  (e.g.
"beta", "live", etc.)..
  
Instead of updating your code so that every time you index a document you make
sure the right index name is specified, what if the client automatically
prefixed the index name with the name of the current Laravel environment?

First, set up the package configuration like so:

```php
return [

	'defaultClass' => 'Cviebrock\LaravelElasticsearchHandlers\Client',

	'connections' => [
		'default' => [
			'MyEnvironmentIndexPrefixHandler'
		]

	]

];
```

Then create the Handler class (extending the BaseHandler class):

```php
class MyEnvironmentIndexPrefixHandler extends Cviebrock\LaravelElasticsearchHandlers\Handlers\BaseHandler {

	/**
	 * The client methods this handler intercepts.
	 *
	 * @var array
	 */
	protected $handledMethods = [
		'index'
	];

	/**
	 * Auto-prefix the document index name with the current Laravel
	 * environment.
	 * 
	 * @param array $parameters
	 * @return array
	 */
	public function index($parameters) {

		if ($index = array_get($parameters, 'index')) {
			$environment = mb_strtolower(preg_replace('/[^a-z0-9_\-]+/', '-', \App::environment()));
			$parameters['index'] = trim($environment, '-') . '-_' . $index;
		}

		return [ $parameters ];
	}
}
```

Now, every time you index a document using the default Elasticsearch connection, 
the current Laravel environment name will be prefixed to the _index_ key of your 
data array.

```php
$data = [
    'index' => 'my-index',
    'type' => 'my-doctype',
    'body' => [
        'content' => 'Lorem ipsum',
    ]
];
$return = Elasticsearch::index($data);
```

This returns:

```
array (size=5)
  '_index' => string 'local-my-index' (length=14)
  '_type' => string 'my-doctype' (length=10)
  '_id' => string 'AU3U9R3kOpwouG512345' (length=20)
  '_version' => int 1
  'created' => boolean true
```

The _index_ was prepended automatically, so your application will work across
all environments without checks or changes.

Also, you can register more than one handler per connection, which means that
the functionality is "chainable".  E.g., prepend the environment to the _index_,
and also add some default parameters to the _body_, etc.. 

Note that our handler's `index()` method accepts the same parameter array that
would be passed to the client, but returns that array wrapped in another array. 
This is required so that the chaining works (since those `$parameters` are
possibly going to run through PHP's `call_user_func_array()` method another few
times. Basically, all handler methods should return an array that corresponds to 
the parameters that were passed to the method.


<a name="special-boot-method"></a>
### Special `boot` Method

Handlers can also define a `boot` method with the following signature:

```php
public function boot(\Elasticsearch\Client $client) {}
```

This is method is run when the handler is registered so it could be used, for
example, to alter the behaviour of the client upon instantiation.  The base 
Elasticsearch client is passed in as the only parameter.


<a name="pre-defined-handlers"></a>
## Pre-Defined Handlers

The package ships with a few pre-defined handlers, all of which are in the
`Cviebrock\LaravelElasticsearchHandlers\Handlers` namespace.

<a name="environment-index-prefix-handler"></a>
### EnvironmentIndexPrefixHandler

Operates on the follow methods:

* index

This is basically the same handler as used in the example above.  It will take
the current Laravel environment, mangles it a bit so it matches Elasticsearch's
constraints for index names, and prepends it to the _index_ key in the given
document.

<a name="index-template-handler"></a>
### IndexTemplateHandler

Operates on the following methods:

* boot-only

This handler reads in the configuration file on boot and sets up any index
templating that's defined in the configuration file.  See the default
configuration file for an example.




<a name="bugs"></a>
## Bugs, Suggestions and Contributions

Please use Github for bugs, comments, suggestions.

1. Fork the project.
2. Create your bugfix/feature branch and write your (well-commented) code.
3. Commit your changes (and your tests) and push to your branch.
4. Create a new pull request against the `master` branch.


<a name="copyright"></a>
## Copyright and License

Laravel-Elasticsearch-Handlers was written by Colin Viebrock and released under the MIT License. See the LICENSE file for details.

Copyright 2015 Colin Viebrock
