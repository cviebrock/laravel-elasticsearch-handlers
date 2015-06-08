<?php namespace Cviebrock\LaravelElasticsearchHandlers\Handlers;

use Cviebrock\LaravelElasticsearchHandlers\Client;


/**
 * Class IndexTemplateHandler
 *
 * @package Cviebrock\LaravelElasticsearchHandlers\Handlers
 */
class IndexTemplateHandler extends BaseHandler {

	/**
	 * The client methods this handler intercepts.
	 *
	 * @var array
	 */
	protected $handledMethods = [];

	/**
	 * @var
	 */
	private $config;

	/**
	 * The configuration array.
	 *
	 * @param array $config
	 */
	public function __construct(array $config) {

		$this->config = $config;
	}

	/**
	 * Use the underlying client to make sure all the index templates are in place.
	 *
	 * @param Client $client
	 */
	public function boot(Client $client) {

		dd($this);

	}
}
