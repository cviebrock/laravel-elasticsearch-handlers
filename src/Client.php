<?php namespace Cviebrock\LaravelElasticsearchHandlers;

use Elasticsearch\Client as BaseClient;


/**
 * Class Client
 *
 * @package Cviebrock\LaravelElasticsearchHandlers
 */
class Client {

	/**
	 * The base Elasticsearch client.
	 *
	 * @var BaseClient
	 */
	protected $client;

	/**
	 * The array of configured handlers.
	 *
	 * @var array
	 */
	protected $handlers = [];

	/**
	 * @param BaseClient $client
	 * @param array $handlers
	 */
	public function __construct(BaseClient $client, array $handlers = []) {

		$this->client = $client;
		$this->registerHandlers($handlers);

		$this->boot();
	}

	protected function boot() {
		foreach ($this->handlers as $handler) {
			if (method_exists($handler,'boot')) {
				$handler->boot($this);
			}
		}
	}

	public function registerHandlers(array $handlers = []) {
		foreach ($handlers as $handlerClass => $configuration) {

			if (is_numeric($handlerClass)) {
				$handlerClass = $configuration;
				$class = new $handlerClass;
			} else {
				$class = new $handlerClass($configuration);
			}

			$this->handlers[$handlerClass] = $class;
			$this->mergeHandledMethods($handlerClass);
		}
	}

	protected function mergeHandledMethods($handlerClass) {
		$methods = $this->handlers[$handlerClass]->getHandledMethods();

		array_walk($methods, function ($methodName) use ($handlerClass) {
			$this->handledMethods[$methodName][] = $handlerClass;
		});
	}

	/**
	 * Get the list of all handlers that should be run for the given method.
	 *
	 * @param $methodName
	 * @return array
	 */
	protected function getHandlers($methodName) {
		return array_get($this->handledMethods, $methodName, []);
	}

	/**
	 * Magic method to pass commands through handlers before sending them to the base client.
	 *
	 * @param $method
	 * @param $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {

		// First, run the parameters through any registered handlers.
		foreach ($this->getHandlers($method) as $handler) {
			$parameters = call_user_func_array([$this->handlers[$handler], $method], $parameters);
		}

		// Finally, run the parameters through the base client.
		return call_user_func_array([$this->client, $method], $parameters);
	}
}
