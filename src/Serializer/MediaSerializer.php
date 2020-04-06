<?php
namespace Philip0514\Ark\Serializer;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;

class MediaSerializer extends Collection
{
	use Serializer;

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
			$rows1 = config('api.media');
			for($i=0; $i<sizeof($rows1); $i++){
				$path[ $rows1[$i] ] = sprintf('%s%s/%s/%s/%s', config('ark.media.root'), config('ark.media.upload'), $rows1[$i], $month, $name);
			}

			return $path;
		});

		$data = $this->manager($resource);
		
		return $data;
	}
}