<?php
namespace Philip0514\Ark\Repositories\Web;

use Philip0514\Ark\API\User;

class UserRepository
{
	function __construct() {
		$this->user = new User();
	}

	public function login($username, $password)
	{
        $result = $this->user->login([
			'client_id'		=>	config('ark.api.password.id'),
			'client_secret'	=>	config('ark.api.password.secret'),
			'username'		=>	$username,
			'password'		=>	$password,
		]);

		return $result;
	}

	public function register($username, $password, $name)
	{
        $result = $this->user->register([
			'client_id'		=>	config('ark.api.password.id'),
			'client_secret'	=>	config('ark.api.password.secret'),
			'username'		=>	$username,
			'password'		=>	$password,
			'name'			=>	$name,
		]);

		return $result;
	}

	public function registerValidate($username)
	{
        $result = $this->user->registerValidate([
			'username'		=>	$username,
		]);

		return $result;
	}
}