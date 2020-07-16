<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\API\V1_0\PageRepository;
use Philip0514\Ark\Repositories\API\V1_0\UserRepository;

//Traits
use Philip0514\Ark\Traits\Response;

class PageController extends Controller
{
	use Response;

	private $repo;

	function __construct(
		PageRepository $PageRepository,
		UserRepository $UserRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->page = $PageRepository;
		$this->repo->user = $UserRepository;
    }

    public function index(Request $request)
    {
		try{
			$token = $this->repo->user->parse_token($request);
			$user_id = $token['user_id'];

			$url = $request->input('url', null);

			$html = $this->repo->page->get($url, $user_id);

			return $this->responseSuccess([
				'data'	=>	null,
				'html'	=>	$html,
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
    }
}