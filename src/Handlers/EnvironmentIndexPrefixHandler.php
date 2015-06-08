<?php namespace Cviebrock\LaravelElasticsearchHandlers\Handlers;


/**
 * Class EnvironmentIndexPrefixHandler
 *
 * @package Cviebrock\LaravelElasticsearchHandlers\Handlers
 */
class EnvironmentIndexPrefixHandler extends BaseHandler {

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
	public function index(array $parameters) {

		if ($index = array_get($parameters, 'index')) {
			$environment = mb_strtolower(preg_replace('/[^a-z0-9_\-]+/', '-', \App::environment()));
			$parameters['index'] = trim($environment, '_-') . '-' . $index;
		}

		return [$parameters];
	}
}
