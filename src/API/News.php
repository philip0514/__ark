<?php
namespace Philip0514\Ark\API;

class News extends Base
{
	protected $urls = [
		'index' => [
			'method'	=> 'GET',
			'api'		=> 'news',
			'values' 	=> [
				'page' 		=>	1,
				'limit'		=>	10,
			],
		],
		'show' => [
			'method'	=> 'GET',
			'api'		=> 'news/{id}',
		],
	];
}