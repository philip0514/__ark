<?php
namespace Philip0514\Ark\Serializer\API\V1_0;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

class UserSerializer extends Collection
{
	use Serializer;
	
	public function collection($data)
	{
		$resource = new Collection($data, function($data){
			$data = (object)$data;

			return [
				'id'				=>	(int)$data->id,
				'name'				=>	htmlspecialchars_decode($data->name),
				'description'		=>	htmlspecialchars_decode($data->description),
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}
	
	
	public function info($data)
	{
		return [
			'id'				=>	(int)$data->id,
			'name'				=>	$data->name,
			'description'		=>	$data->description,
			"email"				=>	$data->email,
			"facebook_id"		=>	$data->facebook_id,
			"twitter_id"		=>	$data->twitter_id,
			"google_id"			=>	$data->google_id,
			"gender"			=>	$data->gender,
			"birthday"			=>	$data->birthday,
			'create_time'		=>	($data->created_at)?$data->created_at->timestamp:null,
		];
	}
	
	public function search($data)
	{
		$resource = new Collection($data, function($data){
			$data = (object)$data;

			return [
				'id'				=>	(int)$data->id,
				'name'				=>	$data->name,
				'description'		=>	$data->description,
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}

	public function clientToken($data)
	{
		return [
			'token'				=>	[
				'token_type'		=>	$data['token_type'],
				'expires_in'		=>	$data['expires_in'],
				'access_token'		=>	$data['access_token'],
			],
		];
	}

	public function passwordToken($data)
	{
		$user = $data['user'];
		return [
			'user'				=>	[
				'id'				=>	$user['id'],
				'name'				=>	$user['name'],
				'email'				=>	$user['email'],
                'facebook_id'		=>	$user['facebook_id'],
                'google_id'			=>	$user['google_id'],
                'twitter_id'		=>	$user['twitter_id'],
				'gender'			=>	$user['gender'],
				'birthday'			=>	$user['birthday'],
			],
			'token'				=>	[
				'token_type'		=>	$data['token_type'],
				'expires_in'		=>	$data['expires_in'],
				'access_token'		=>	$data['access_token'],
				'refresh_token'		=>	$data['refresh_token'],
			],
		];
	}

	public function refreshToken($data)
	{
		return [
			'token'				=>	[
				'token_type'		=>	$data['token_type'],
				'expires_in'		=>	$data['expires_in'],
				'access_token'		=>	$data['access_token'],
				'refresh_token'		=>	$data['refresh_token'],
			],
		];
	}
}