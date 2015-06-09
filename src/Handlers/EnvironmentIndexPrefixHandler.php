<?php namespace Cviebrock\LaravelElasticsearchHandlers\Handlers;


/**
 * Class EnvironmentIndexPrefixHandler
 *
 * @package Cviebrock\LaravelElasticsearchHandlers\Handlers
 */
class EnvironmentIndexPrefixHandler extends BaseHandler {

	/**
	 * Auto-prefix the document index name with the current Laravel
	 * environment.
	 *
	 * @param array $parameters
	 * @return array
	 */
	public function handleIndex(array $parameters) {

		if ($index = array_get($parameters, 'index')) {
			$environment = mb_strtolower(preg_replace('/[^a-z0-9_\-]+/', '-', \App::environment()));
			$parameters['index'] = trim($environment, '_-') . '-' . $index;
		}

		return $parameters;
	}
}
