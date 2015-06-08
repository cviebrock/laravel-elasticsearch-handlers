<?php namespace Cviebrock\LaravelElasticsearchHandlers;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;


/**
 * Class ServiceProvider
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class ServiceProvider extends BaseServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot() {

		$app = $this->app;

		if (version_compare($app::VERSION, '5.0') >= 0) {
			// Laravel 5
			$configPath = realpath(__DIR__ . '/../config/elasticsearch-handlers.php');
			$this->publishes([
				$configPath => config_path('elasticsearch-handlers.php')
			]);
		}
	}

	/**
	 * Register the service provider.  This will rebind the base Elasticsearch Manager
	 * with this package's version, that wraps the ES client ... allowing us to
	 * modify the base functionality before sending data to ES or after reading it.
	 *
	 * @return void
	 */
	public function register() {

		$this->app->bindShared('elasticsearch', function ($app) {
			return new Manager($app, $app['elasticsearch.factory']);
		});
	}
}
