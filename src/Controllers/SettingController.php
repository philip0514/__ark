<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\SettingRepository as MainRepo;

class SettingController extends Controller
{
    protected 	$repo, 
				$config,
				$method = 'get',
				$route_index;

	function __construct(
        Request $request,
		MainRepo $main
	)
	{
		parent::__construct();
        $this->repo->main = $main;
        $this->method = strtolower($request->method());

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'系統設定',
			'route'				=>	$route,
			'controller'		=>	$controller,
        ];

		$this->config();
    }

    public function index(Request $request)
    {
        switch($this->method){
            case 'post':
                $title = $request->input('title', null);
                $keyword = $request->input('keyword', null);
                $description = $request->input('description', null);
				$tag = $request->input('tag', null);
				$ogimage_input = $request->input('ogimage_input', 0);

				$data = [
					'id'			=>	1,
					'title'			=>	$title,
					'description'	=>	$description,
                    'tag'           =>  $tag,
                    'ogimage_input' =>  $ogimage_input,
				];
                $this->repo->main->update($data);
                return redirect()->route($this->route_index);

                exit;
            break;
        }

		$rows1 = $this->repo->main->single(1);
        $tag = isset($rows1['tags']) ? $rows1['tags'] : [];

        $rows2 = isset($rows1['ogimages']) ? $this->repo->main->media($rows1['ogimages']) : [
            'id'    =>  null,
            'data'  =>  [],
        ];
        $rows1['ogimage_input'] = $rows2['id'];
        $rows1['ogimage_data'] = $rows2['data'];

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
            'tag'       =>  $tag,
        ];
        return $this->view($this->config['html']['single'], $data);
    }
}