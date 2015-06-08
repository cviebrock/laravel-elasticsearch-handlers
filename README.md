# Laravel Elasticsearch Handler

You're using [Laravel Elasticsearch](//github.com/cviebrock/laravel-elasticsearch) 
to make it easy to create an ES client in your Laravel application, right?  Well, 
you should also be using this package to make _using_ that client easier!


## Installation


1. Install the `cviebrock/laravel-elasticsearch-handler` package via composer:

    ```shell
    $ composer require cviebrock/laravel-elasticsearch-handler
    ```
    
2. Publish the configuration file.  For Laravel 4:

    ```shell
    php artisan config:publish cviebrock/laravel-elasticsearch-handler
    ```

    Or for Laravel 5:

    ```shell
    php artisan vendor:publish cviebrock/laravel-elasticsearch-handler
    ```

3. Add the service provider (`app/config/app.php` for Laravel 4, `config/app.php` for Laravel 5):

    ```php
    # Add the service provider to the `providers` array
    'providers' => array(
        ...
        'Cviebrock\LaravelElasticSearchHandler\ServiceProvider',
    )

    # Add the facade to the `aliases` array
    'aliases' => array(
        ...
        'ElasticsearchHandler' => 'Cviebrock\LaravelElasticSearchHandler\Facade',
    )
    ```



## Usage

The ElasticsearchHandler is basically a decorator class for the Elasticsearch
client. It intercepts (some of) your ES commands, transforms the query or payload,
and passes it through.

First you will need to create the decorated client by passing in a `\Elasticsearch\Client`
class:

```php
$elastic = ElasticsearchHandler::make( \Elasticsearch\Client $client );
```

Alternatively, if you are using the [Laravel Elasticsearch](//github.com/cviebrock/laravel-elasticsearch)
package (you are, aren't you?), then you can build the client and decorate it all
in one go just by passing in the connection string defined in your Laravel-Elasticsearch
configuration:

```php
$elastic = ElasticsearchHandler::make( 'connectionName' );

// or the default connection:

$elastic = ElasticsearchHandler::make();
```
