<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

//Models
use Philip0514\Ark\Models\Zip;

class ZipRepository
{
    function __construct()
    {
        $this->zip = new Zip();
    }

    public function city()
    {
        $data = $this->zip->where('city_id', 0)->get()->toArray();

        return $data;
    }

    public function area($city_id)
    {
        $data = $this->zip->where('city_id', $city_id)->get()->toArray();

        return $data;
    }

    public function findByID($id)
    {
        $data = $this->zip->find($id);

        if($data){
            $data = $data->toArray();
        }

        return $data;
    }
}