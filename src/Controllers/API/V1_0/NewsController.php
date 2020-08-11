<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

//Repository
use Philip0514\Ark\Repositories\API\V1_0\UserRepository;
use Philip0514\Ark\Repositories\API\V1_0\PageRepository;
use Philip0514\Ark\Repositories\API\V1_0\NewsRepository;

//Traits
use Philip0514\Ark\Traits\Response;

//Serializer
use Philip0514\Ark\Serializer\API\V1_0\NewsSerializer;

class NewsController extends Controller
{
	use Response;

	private $repo;

	function __construct(
		UserRepository $UserRepository,
		PageRepository $PageRepository,
		NewsRepository $NewsRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->user = $UserRepository;
		$this->repo->page = $PageRepository;
		$this->repo->news = $NewsRepository;
    }

    public function index(Request $request)
    {
		try{
			$page = $request->get('page', 1);
			$limit = $request->get('limit', 10);
			$start = $limit*($page-1);

			$token = $this->repo->user->parse_token($request);
			$user_id = $token['user_id'];

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

			$meta = $this->repo->page->meta([
				'title'		=>	'最新消息',
            ]);

			return $this->responseSuccess([
				'data'			=>	$result,
				'pagination'	=>	$pagination,
				'meta'			=>	$meta,
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
			$token = $this->repo->user->parse_token($request);
			$user_id = $token['user_id'];

			$result = $this->repo->news->show($id);

			if(isset($result["error"])){
				throw new Exception($result["error"]);
			}

			$serializer = new NewsSerializer();
			$result = $serializer->show($result);

			$meta = $this->repo->page->meta([
				'title'			=>	$result['name'],
				'description'	=>	$result['description'],
				'ogimage'		=>	$result['ogimage'],
			]);
			unset($result['ogimage']);

			return $this->responseSuccess([
				'data'			=>	$result,
				'meta'			=>	$meta,
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}
}