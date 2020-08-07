<?php
namespace Philip0514\Ark\Serializer;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

class TagSerializer extends Collection
{
	use Serializer;

	public function collection($data)
	{
		$resource = new Collection($data, function($data) {
			$data = (object)$data;

			return [
				'id'				=>	(int)$data->id,
				'name'				=>	$data->name,
				'slug'				=>	$data->slug,
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}
}