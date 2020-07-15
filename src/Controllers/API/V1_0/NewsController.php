<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use Philip0514\Ark\Repositories\API\V1_0\NewsRepository;
use Philip0514\Ark\Repositories\API\V1_0\PageRepository;

//Traits
use Philip0514\Ark\Traits\Response;

//Serializer
use Philip0514\Ark\Serializer\API\V1_0\NewsSerializer;

class NewsController extends Controller
{
	use Response;

	private $repo;

	function __construct(
		NewsRepository $NewsRepository,
		PageRepository $PageRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->news = $NewsRepository;
		$this->repo->page = $PageRepository;
    }

    public function index(Request $request)
    {
		try{
			$page = $request->get('page', 1);
			$limit = $request->get('limit', 10);
			$start = $limit*($page-1);

			$data = $this->repo->news->index($start, $limit);

			//$data = $this->repo->page->meta($slug);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}
			$count = $data['count'];
			$result = $data['result'];

			$serializer = new NewsSerializer();
            $result = $serializer->collection($result);

			$paginate = new LengthAwarePaginator($result, $count, $limit, $page, ['path' => url(implode('/', [env('API_PREFIX'), 'news']))]);
			$paginate->appends([
				'limit' 		=> $limit, 
			])->links();
			$pagination = $this->pagination($paginate);

			return $this->responseSuccess([
				'data'			=>	$result,
				'pagination'	=>	$pagination,
				//'meta'			=>	$meta,
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
    }

    public function show(Request $request, $id)
    {
		try{
			$result = $this->repo->news->show($id);

			if(isset($result["error"])){
				throw new Exception($result["error"]);
			}

			return $this->responseSuccess([
				'data'	=>	$result,
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}
}