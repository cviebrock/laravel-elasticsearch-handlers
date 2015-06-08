<?php namespace Cviebrock\LaravelElasticsearchHandlers;

use Elasticsearch\Client as BaseClient;


class Client {

	/**
	 * @var BaseClient
	 */
	protected $client;

	public function __construct(BaseClient $client) {

		$this->client = $client;
	}


	public function __call($method, $parameters) {
		return call_user_func_array([$this->client, $method], $parameters);
	}

}
