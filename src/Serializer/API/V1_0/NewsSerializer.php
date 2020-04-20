<?php
namespace Philip0514\Ark\Serializer\API\V1_0;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

class NewsSerializer extends Collection
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
}