<?php
namespace Philip0514\Ark\API;

class Page extends Base
{
	protected $urls = [
		'get' => [
			'method'	=> 'GET',
			'api'		=> 'page',
			'values' 	=> [
                'url' 		=> 'index',
			],
		],
	];
}