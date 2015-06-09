<?php namespace Cviebrock\LaravelElasticsearchHandlers;

use Elasticsearch\Client as BaseClient;
use ReflectionClass;
use ReflectionMethod;


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
	 * List of client methods that are handled, and which handlers handle them.
	 *
	 * @var array
	 */
	protected $handledMethods = [];


	/**
	 * @param BaseClient $client
	 * @param array $handlers
	 */
	public function __construct(BaseClient $client, array $handlers = []) {

		$this->client = $client;
		$this->registerHandlers($handlers);
	}

	/**
	 * Register the handlers for the client.
	 *
	 * @param array $handlers
	 */
	public function registerHandlers(array $handlers = []) {

		foreach ($handlers as $handlerClass => $configuration) {

			// If the handler takes a configuration, load that from the array and
			// build the class. If not, then just build the class.

			if (is_numeric($handlerClass)) {
				$handlerClass = $configuration;
				$class = new $handlerClass;
			} else {
				$class = new $handlerClass($configuration);
			}

			// Use reflection to figure out what Elasticsearch methods the handler handles.

			$reflect = new ReflectionClass($class);

			// Find the handled methods and merge them into the client's list.

			$classMethods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

			array_walk($classMethods, function ($classMethod) use ($handlerClass) {
				if (starts_with($classMethod->name, 'handle')) {
					$method = lcfirst(substr($classMethod->name, 6));

					$this->handledMethods[$method][] = $handlerClass;
				}
			});

			// Save the handler instance.

			$this->handlers[$handlerClass] = $class;
		}

		// Boot all the handlers with the client instance.

		foreach ($this->handlers as $handler) {
			$handler->boot($this);
		}
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

		$parameterCount = count($parameters);

		// First, find out which handlers are registered for this method.

		$handlers = $this->getHandlers($method);

		// And get the real class method name we will call.

		$classMethod = 'handle' . ucfirst($method);

		// Then run the parameters through any registered handlers.

		foreach ($handlers as $handler) {
			$parameters = call_user_func_array([$this->handlers[$handler], $classMethod], $parameters);
			if ($parameterCount == 1) {
				$parameters = [$parameters];
			}
		}

		// Finally, run the parameters through the base client.
		return call_user_func_array([$this->client, $method], $parameters);
	}
}
