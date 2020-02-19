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
}