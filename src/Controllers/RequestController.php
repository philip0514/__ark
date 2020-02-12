<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;
use Philip0514\Ark\Repositories\ZipRepository;

class RequestController extends Controller
{
    protected 	$repo;

	function __construct(
		ZipRepository $zip
	){
		parent::__construct();
		$this->repo->zip = $zip;
    }

    public function toggle_sidebar(Request $request)
    {
        $admin = session()->get('admin');
        $sidebar = 0;
        if(isset($admin['config']['sidebar'])){
            $sidebar = $admin['config']['sidebar'];
        }

        $admin['config']['sidebar'] = $sidebar ? 0 : 1;

        session()->put('admin', $admin);
    }

    public function zip(Request $request)
    {
        $id = $request->input('id', 0);

        $area = $this->repo->zip->area($id);

        echo json_encode($area, JSON_UNESCAPED_UNICODE);
    }
}