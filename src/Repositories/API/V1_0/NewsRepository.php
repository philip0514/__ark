<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Philip0514\Ark\Models\News as Model;

class NewsRepository
{
	function __construct()
	{
        $this->model = new Model();
    }

    public function index($start=0, $limit=10)
    {
        $rows1 = $this->model
        ->with([
            'ogimages'  =>  function($query){
                $query->orderBy('sort', 'asc');
            }
        ])
        ->where('display', 1)
        ->offset($start)
        ->limit($limit)
        ->orderBy('id', 'desc')
        ->get()->toArray();

        return $rows1;
    }
}
