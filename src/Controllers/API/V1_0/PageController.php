<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\API\V1_0\PageRepository;

//Traits
use Philip0514\Ark\Traits\Response;

class PageController extends Controller
{
	use Response;

	private $repo;

	function __construct(
		PageRepository $PageRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->page = $PageRepository;
    }

    public function index(Request $request)
    {
		try{
            $slug = $request->input('slug', null);

			$data = $this->repo->page->meta($slug);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			return $this->responseSuccess([
				'data'	=>	$data
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
    }
}