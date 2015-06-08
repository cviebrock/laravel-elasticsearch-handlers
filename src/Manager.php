<?php namespace Cviebrock\LaravelElasticsearchHandlers;

use Cviebrock\LaravelElasticsearch\Manager as BaseManager;


/**
 * Class Manager
 *
 * @package Cviebrock\LaravelElasticsearch
 */
class Manager extends BaseManager {

	/**
	 * Make a new connection, and wrap the client in our handler Client.
	 *
	 * @param $name
	 * @return \Elasticsearch\Client|mixed
	 */
	protected function makeConnection($name) {

		$baseClient = parent::makeConnection($name);

		$client = new Client($baseClient);

		$handlerConfig = $this->getHandlerConfig($name);

		// no configuration
		if ($handlerConfig === null) {
			return $client;
		}

	}

	protected function getHandlerConfig($name) {

		$connections = $this->app['config']['elasticsearch-handlers.connections'];

		if (is_null($config = array_get($connections, $name))) {
			return null;
		}

		return $config;
	}

}
