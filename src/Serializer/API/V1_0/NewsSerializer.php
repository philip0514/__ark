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
				'news_time'			=>	$data->news_time,
				'media'				=>	$media,
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}
	
	public function show($data)
	{
		$data = (object)$data;
		$serializer = new MediaSerializer();
		$media = $serializer->collection($data->media);
		$ogimage = $serializer->ogimage($data->ogimages);

		return [
			'id'				=>	(int)$data->id,
			'name'				=>	htmlspecialchars_decode($data->name),
			'description'		=>	htmlspecialchars_decode($data->description),
			'news_time'			=>	$data->news_time,
			'media'				=>	$media,
			'content'			=>	$data->html,
			'ogimage'			=>	$ogimage,
		];
	}
}