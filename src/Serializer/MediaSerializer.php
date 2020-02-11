<?php
namespace Philip0514\Ark\Serializer;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

class MediaSerializer extends Collection
{
	use Serializer;

	public function data($data)
	{
		$resource = new Collection($data, function($data) {
			$data = (object)$data;

			$name = $data->name;
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);
			$path = sprintf('%s%s/square/%s/%s', config('ark.media.root'), config('ark.media.upload'), $month, $name);

			return [
				'id'				=>	(int)$data->id,
				'name'				=>	$name,
				'title'				=>	$data->title,
				'path'				=>	$path,
				'file_size'			=>	$data->file_size,
				'created_at'		=>	$data->created_at
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}
}