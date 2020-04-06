<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;
use Philip0514\Ark\Repositories\Dashboard\ZipRepository;

class RequestController extends Controller
{
    protected 	$repo;

	function __construct(
		ZipRepository $zip
	){
		parent::__construct();
		$this->repo->zip = $zip;
    }

    public function toggleSidebar(Request $request)
    {
        $sidebar = $request->session()->get('ark.config.sidebar');
        if($sidebar){
            $request->session()->forget('ark.config.sidebar');
        }else{
            $request->session()->put('ark.config.sidebar', 1);
        }
    }

    public function zip(Request $request)
    {
        $id = $request->input('id', 0);

        $area = $this->repo->zip->area($id);

        return response()->json($area);
    }
}