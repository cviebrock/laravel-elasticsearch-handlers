# Laravel-Elasticsearch-Handlers

An even easier way to use the official Elastic Search client in your Laravel applications.


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
    

## Usage

This package extends the `laravel-elasticsearch` package by returning a "decorated" 
Elasticsearch client class, instead of the default PHP client.  You can configure
how the client is decorated on a per-connection basis.
