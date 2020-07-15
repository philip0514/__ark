<?php
namespace Philip0514\Ark\Serializer\API\V1_0;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

//Serializer
use Philip0514\Ark\Serializer\MediaSerializer;

class NewsSerializer extends Collection
{
	use Serializer;
	
	public function collection($data)
	{
		$resource = new Collection($data, function($data){
			$data = (object)$data;

			$serializer = new MediaSerializer();
			$media = $serializer->collection($data->media);
			if($media){
				$media = $media[0];
			}

			return [
				'id'				=>	(int)$data->id,
				'name'				=>	htmlspecialchars_decode($data->name),
				'description'		=>	htmlspecialchars_decode($data->description),
				'media'				=>	$media,
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}
}