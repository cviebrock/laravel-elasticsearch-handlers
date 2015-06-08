<?php namespace Cviebrock\LaravelElasticsearchHandlers\Handlers;


class BaseHandler {

	protected $handledMethods = [];

	public function getHandledMethods() {
		return $this->handledMethods;
	}

}
