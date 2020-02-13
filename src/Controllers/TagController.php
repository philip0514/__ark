<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\TagRepository as MainRepo;

class TagController extends Controller
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
			'name'				=>	'標籤',
			'route'				=>	$route,
			'controller'		=>	$controller,
			'action'			=>	[
				'create'			=>	1,
				'update'			=>	1,
				'delete'			=>	1,
				'softDelete'		=>	1,
				'display'			=>	1,
				'sort'				=>	0,
				'import'			=>	0,
				'export'			=>	0,
				'search'			=>	0,
				'autocomplete'		=>	0,
            ],
			'column'			=>	[
				[
					'name'			=>	'select_all',
					'width'			=>	'20px',
					'field'			=>	'select',
                    'visible'		=>	[false, false],
                    'orderby'       =>  null,
					'orderable'		=>	false,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'#',
					'width'			=>	'60px',
					'field'			=>	'id',
					'visible'		=>	[true, true],
					'orderby'		=>	['id', 'desc'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'名稱',
					'field'			=>	'name',
					'visible'		=>	[true, true],
					'orderby'		=>	['name'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'新增時間',
					'width'			=>	'70px',
					'field'			=>	'created_at',
					'visible'		=>	[true, true],
					'orderby'		=>	['created_at'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'更新時間',
					'width'			=>	'70px',
					'field'			=>	'updated_at',
					'visible'		=>	[true, true],
					'orderby'		=>	['updated_at'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'刪除時間',
					'width'			=>	'70px',
					'field'			=>	'deleted_at',
					'visible'		=>	[true, true],
					'orderby'		=>	['deleted_at'],
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

    public function single(Request $request, $id=null)
    {
		$this->permissionCheck();

        switch($this->method){
            case 'post':
				$id = $request->input('id', 0);
                $name = $request->input('name');
				$deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$method = $request->input('__method', 0);

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'slug'			=>	$this->short_name($name),
					'deleted'		=>	$deleted,
					'display'		=>	$display,
				];

                if(!$id){
                    $id = $this->repo->main->create($data);
                }else{
                    $this->repo->main->update($data);
				}

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

			$rows1 = $this->repo->main->editor($rows1);
		}

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
        ];
        return $this->view($this->config['html']['single'], $data);
	}

	public function search(Request $request)
	{
		$term = $request->get('term');

		$rows1 = $this->repo->main->search($term);

		$data = array(
			'results'	=>	$rows1
		);
		echo json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	public function insert(Request $request)
	{
		$text = $request->input('text');

		$id = $this->repo->main->create([
			'name'		=>	$text,
			'slug'		=>	$this->short_name($text),
		]);
		
		echo json_encode([
			'id'	=>	$id, 
			'text'	=>	$text
		]);
	}

	private function short_name($name)
	{
		$name = str_replace(' ', '', preg_replace(config('ark.url_allow_chars'), '' , strtolower(trim($name))));
	
		return $name;
	}
}