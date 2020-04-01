<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Dashboard\AdministratorRepository as MainRepo;

class AdministratorController extends Controller
{
    protected 	$repo, 
				$config,
				$method = 'get',
				$route_index = 'administrator.index';

	function __construct(
        Request $request,
		MainRepo $main
	)
	{
		parent::__construct();
        $this->repo->main = $main;
        $this->method = strtolower($request->method());

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
        
        $this->config  = [
			'name'				=>	'管理者',
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
					'orderby'		=>	['id', 'asc'],
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
					'name'			=>	'帳號',
					'field'			=>  'account',
					'visible'		=>	[true, true],
					'orderby'		=>	['account'],
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
			'search'			=>	[
				'input'				=>	[
					'search', 'display',
                ],
				'field'				=>	[
					'name', 'account',
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
                $account = $request->input('account', null);
                $password = $request->input('password', null);
                $name = $request->input('name');
                $id = $request->input('id', 0);
                $deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$role = $request->input('role', []);

				$data = [
					'id'		=>	$id,
					'account'	=>	$account,
					'password'	=>	$password,
					'name'		=>	$name,
					'display'	=>	$display,
					'role'		=>	$role,
					'deleted'	=>	$deleted,
				];
				$id = $this->repo->main->save($data);

                return redirect()->route($this->route_index);
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

		$rows2 = $this->repo->main->role();

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
            'rows2'     =>  $rows2,
        ];
        return $this->view($this->config['html']['single'], $data);
    }

    public function validate(Request $request)
    {
        /**
         * false: 已存在
         * true: 可使用
         */
        $type = $request->input('type', null);
        $account = $request->input('account', null);
		$id = $request->input('id', null);
		
		switch($type){
			case 'account':
				echo $this->repo->main->validate($account, $id);
			break;
		}
    }

    public function profile(Request $request)
    {
		$config = $this->config;
		$config['name'] = 'Profile';

		$rows1 = $this->repo->main->profile();
        switch($this->method){
            case 'post':
                $account = $request->input('account', null);
                $password = $request->input('password', null);
                $name = $request->input('name');
                $id = $rows1['id'];

				$data = [
					'id'		=>	$id,
					'account'	=>	$account,
					'password'	=>	$password,
					'name'		=>	$name,
				];
				$this->repo->main->profile_update($data);
				
                return redirect()->route('administrator.profile');
            break;
        }

		$data = [
			'config'	=>	$config,
			'rows1'		=>	$rows1,
		];
        return $this->view('ark::administrator.profile', $data);
	}
}