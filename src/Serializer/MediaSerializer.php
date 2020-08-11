<?php
namespace Philip0514\Ark\Serializer;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;
use Philip0514\Ark\Traits\Helper;

class MediaSerializer extends Collection
{
	use Serializer, Helper;

	public function data($data, $size='square')
	{
		$resource = new Collection($data, function($data) use ($size) {
			$data = (object)$data;

			$name = $data->name;
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);
			$path = sprintf('%s%s/%s/%s/%s', config('ark.media.root'), config('ark.media.upload'), $size, $month, $name);

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

	public function path($data, $size='square')
	{
		$resource = new Collection($data, function($data) use ($size) {
			$data = (object)$data;

			$name = $data->name;
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);
			$path = sprintf('%s%s/%s/%s/%s', config('ark.media.root'), config('ark.media.upload'), $size, $month, $name);

			return [
				'path'	=>	$path,
			];
		});

		$data = $this->manager($resource);
		
		return $data;
	}

	public function collection($data)
	{
		if(!$data){
			return null;
		}
		$resource = new Collection($data, function($data) {
			$data = (object)$data;

			$name = $data->name;
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);

			$path = [];
			$dimensions = array_keys(config('ark.media.dimensions'));

			for($i=0; $i<sizeof($dimensions); $i++){
				$path[ $dimensions[$i] ] = sprintf('%s%s/%s/%s/%s', config('ark.media.root'), config('ark.media.upload'), $dimensions[$i], $month, $name);
			}

			return $path;
		});

		$data = $this->manager($resource);
		
		return $data;
	}

	public function ogimage($data)
	{
		$ogimage = [];
		for($i=0; $i<sizeof($data); $i++){
			$ogimage[] = $this->mediaPath($data[$i]['name'], 'facebook');
		}
		
		return $ogimage;
	}
}