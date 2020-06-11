<?php
namespace Philip0514\Ark\API;

class User extends Base
{
	protected $urls = [
		'login'		=> [
			'method'	=> 'POST',
			'api'		=> 'user/login',
			'values' 	=> [
                'grant_type' 	=>	'password',
                'client_id' 	=>	null,
                'client_secret' =>	null,
                'username' 		=>	null,
                'password' 		=>	null,
			],
		],
		'register'	=>	[
			'method'	=> 'POST',
			'api'		=> 'user/register',
			'values' 	=> [
                'grant_type' 	=>	'password',
                'client_id' 	=>	null,
                'client_secret' =>	null,
                'username' 		=>	null,
				'password' 		=>	null,
				'name'			=>	null,
			],
		],
		'registerValidate' => [
			'method'	=> 'POST',
			'api'		=> 'user/registerValidate',
			'values' 	=> [
                'username' 		=>	null,
			],
		]
	];
}