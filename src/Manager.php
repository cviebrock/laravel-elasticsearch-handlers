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

		// Create a base ES client for the connection.
		$baseClient = parent::makeConnection($name);

		// Find the base client wrapper class.
		$handlerClass = $this->getHandlerClass();

		// Load the handlers for this configuration.
		$handlers = $this->getConnectionHandlers($name);

		// Create a "wrapped" client passing in the appropriate configuration
		return new $handlerClass($baseClient, $handlers);
	}

	/**
	 * Get the default handler class.
	 *
	 * @return string
	 */
	public function getHandlerClass() {
		return $this->app['config']['elasticsearch-handlers.defaultClass'];
	}

	/**
	 * Get the handlers for the given connection name.
	 *
	 * @param string $name
	 * @return array
	 */
	protected function getConnectionHandlers($name) {

		$connections = $this->app['config']['elasticsearch-handlers.connections'];

		return array_get($connections, $name, []);
	}
}
