<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

//Repositories
use Philip0514\Ark\Repositories\UserRepository as MainRepo;

class NewsletterController extends Controller
{
    protected 	$repo, 
				$config,
				$path,
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
        $this->path = $request->path();

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'電子報',
			'route'				=>	$route,
			'controller'		=>	$controller,
			'action'			=>	[
				'create'			=>	0,
				'update'			=>	1,
				'delete'			=>	0,
				'display'			=>	0,
				'sort'				=>	0,
				'import'			=>	0,
				'export'			=>	0,
				'search'			=>	0,
				'autocomplete'		=>	0,
            ],
			'column'			=>	[
				[
					'name'			=>	'#',
					'width'			=>	'60px',
					'field'			=>	'id',
					'visible'		=>	[true, true],
					'orderby'		=>	['id', 'asc'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'姓名',
					'field'			=>	'name',
					'visible'		=>	[true, true],
					'orderby'		=>	['name'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'電子信箱',
					'field'			=>	'email',
					'visible'		=>	[true, true],
					'orderby'		=>	['email'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'編輯',
					'width'			=>	'50px',
					'field'			=>	'update',
					'visible'		=>	[false, false],
                    'orderby'       =>  null,
					'orderable'		=>	false,
					'sortable'		=>	false,
                ],
            ],
        ];

		$this->config();
    }

    protected function datatable(Request $request)
    {
		$config = $this->config;
		$route = $config['route'];
		$path = prefixUri($config['controller']);
		$query = $this->repo->main->newsletter_datatable($request);

		$datatable = Datatables::of($query);

		//update
		$raw_columns[] = 'update';
		if($config['action']['update']){
			$datatable
			->addColumn('update', function($data) use ($path){
				$id = $data->id;
				return sprintf('<a href="%s/%s" class="edit" title="編輯"><i class="fas fa-pen"></i></a>', $path, $id);
			});
		}else{
			$datatable
			->addColumn('update', function(){
				return null;
			});
		}

		return $datatable
			->rawColumns($raw_columns)
			->make(true);
    }

    public function single(Request $request, $id=null)
    {
		$this->permissionCheck();

        switch($this->method){
            case 'post':
				$id = $request->input('id', 0);
				$newsletter = $request->input('newsletter', 0);
				$method = $request->input('__method', 0);

				$data = [
					'id'			=>	$id,
					'newsletter'	=>	$newsletter,
				];
                $this->repo->main->update($data);

				switch($method){
					case 1:
					echo json_encode([
						'id'	=>	$id,
					]);
					break;
					default:
					case 0:
						return redirect()->route($this->route_index);
					break;
				}

                exit;
            break;
        }

        $rows1 = [];

        if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['name'];
		}

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
		];
        return $this->view($this->config['html']['single'], $data);
    }
}