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
		try {
            $result = $this->model
            ->with([
                'ogimages'  =>  function($query){
                    $query->orderBy('sort', 'asc');
                },
                'media'  =>  function($query){
                    $query->orderBy('sort', 'asc');
                }
            ])
            ->where('display', 1);

            $count = $result->count();
            $result = $result->orderby('id', 'desc')->offset($start)->limit($limit)->get()->toArray();

            return [
                'count'		=>	$count,
                'result'	=>	$result,
            ];

        }catch(Exception $e){
            $result = [
                'error' => $e->getMessage()
            ];
            return $result;
        }
    }

	public function single($id)
	{
		try {
			$result = $this->model
				->with([
					'media'		=>	function ($query){
						$query->orderby('sort', 'asc');
					},
					'ogimages'	=>	function ($query){
						$query->orderby('sort', 'asc');
					},
				])
				->where('display', '=', 1)
				->where('id', $id)
				->first();

			if(!$result){
				throw new Exception('news_404');
			}

			if(!isset($result->id)){
				throw new Exception('news_404');
			}else{
				return $result;
			}

		}catch(Exception $e){
			$result = [
				'error' => $e->getMessage()
			];
			return $result;
		}
	}
}
