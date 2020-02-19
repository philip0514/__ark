<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1_0', function ($api) {

	//no auth
	$api->group([
		'namespace' => '\Philip0514\Ark\Controllers\API\V1_0',
	], function ($api) {
		$api->post('oauth/token', 'UserController@token');
        $api->post('oauth/token/refresh', 'UserController@token');

    });

	//client auth
	$api->group([
		'namespace' => '\Philip0514\Ark\Controllers\API\V1_0',
		'middleware' => '\Philip0514\Ark\Middleware\CheckClientCredentials',
	], function ($api) {

		//user
        $api->post('user/login', 'UserController@token');
		$api->post('user/register', 'UserController@register');
		$api->post('user/facebook', 'UserController@facebook');
		$api->post('user/google', 'UserController@google');
		$api->post('user/twitter', 'UserController@twitter');
		$api->post('user/verification', 'UserController@verification');
		$api->post('user/forgot_password', 'UserController@forgot_password');
        
    });

	//password auth
	$api->group([
		'namespace' => '\Philip0514\Ark\Controllers\API\V1_0',
		'middleware' => 'auth:api'
	], function ($api) {
		//user
		$api->get('user/info', 'UserController@infoGet');
		$api->post('user/info', 'UserController@infoPost');
		$api->post('user/logout', 'UserController@logout');

    });

});