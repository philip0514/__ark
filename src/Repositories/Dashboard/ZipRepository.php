<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Models\Zip;

class ZipRepository
{
    protected $model;

	function __construct(
		Zip $zip
	){
		$this->zip = $zip;
	}
	
	public function city()
	{
		$rows1 = $this->zip->where('city_id', 0)->orderBy('sort', 'asc')->get()->toArray();
		return $rows1;
	}

	public function area($city_id)
	{
		$rows1 = $this->zip->where('city_id', $city_id)->orderBy('sort', 'asc')->get()->toArray();

		for($i=0; $i<sizeof($rows1); $i++){
			$rows1[$i]['text'] = $rows1[$i]['code'].' '.$rows1[$i]['name'];
		}

		return $rows1;
	}
}