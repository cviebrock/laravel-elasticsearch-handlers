<?php

return [

	/**
	 * This is the default client class returned from the factory method.
	 * By default, it does nothing but pass commands through handlers and then on
	 * to the base Elasticsearch client.  But you could extend this client class
	 * to do extra stuff, e.g. on instantiation, etc..
	 */

	'defaultClass' => 'Cviebrock\LaravelElasticsearch\Handlers\Client',

	/**
	 * This array should match the array defined in `app\elasticsearch.php`.
	 * The key represents the connection name, and the value is an array of
	 * handlers to apply to that connection's client.  The elements of the array
	 * can either  classnames, or classname=>configurationArray pairs (for those
	 * handlers that require configuration), i.e.:
	 *
	 *  [
	 *    'handlerClass1',
	 *    'handlerClass2' => [ configurationArray ],
	 *    ...
	 *  ]
	 */

	'connections' => [

		/**
		 * The following configuration applies to clients on the "default" connection:
		 */
		'default' => [

			/**
			 * The "EnvironmentIndexPrefixHandler" prefixes the _index_ key with the
			 * current Laravel environment when indexing documents.
			 */

//			'Cviebrock\LaravelElasticsearchHandlers\Handlers\EnvironmentIndexPrefixHandler',

			/**
			 * The "IndexTemplateHandler" makes sure that the configured Elasticsearch
			 * index templates are in place when the client is instantiated.
			 */

//			'Cviebrock\LaravelElasticsearchHandlers\Handlers\IndexTemplateHandler' => [
//
//			]

		]

	]

];
