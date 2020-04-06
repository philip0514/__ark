<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Dashboard\UserRepository as MainRepo;
use Philip0514\Ark\Repositories\Dashboard\ZipRepository;

class UserController extends Controller
{
    protected 	$repo, 
				$config,
				$path,
				$method = 'get',
				$route_index;

	function __construct(
        Request $request,
		MainRepo $main,
		ZipRepository $zipRepo
	)
	{
		parent::__construct();
        $this->repo->main = $main;
        $this->repo->zip = $zipRepo;
        $this->method = strtolower($request->method());
        $this->path = $request->path();

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'會員',
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
					'name'			=>	'姓名',
					'field'			=>	'name',
					'visible'		=>	[true, true],
					'orderby'		=>	['name'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'帳號',
					'field'			=>	'email',
					'visible'		=>	[true, true],
					'orderby'		=>	['email'],
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
					'visible'		=>	[true, false],
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
                $name = $request->input('name', null);
                $email = $request->input('email', null);
                $password = $request->input('password', null);
                $gender = $request->input('gender', 0);
                $birthday = $request->input('birthday', null);
                $city_id = $request->input('city_id', 0);
                $area_id = $request->input('area_id', 0);
                $address = $request->input('address', null);
                $phone = $request->input('phone', null);
                $mobile = $request->input('mobile', null);
				$deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$checked = $request->input('checked', 0);
				$newsletter = $request->input('newsletter', 0);
				$method = $request->input('__method', 0);

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'email'			=>	$email,
					'password'		=>	$password,
					'gender'		=>	$gender,
					'birthday'		=>	$birthday,
					'city_id'		=>	$city_id,
					'area_id'		=>	$area_id,
					'address'		=>	$address,
					'phone'			=>	$phone,
					'mobile'		=>	$mobile,
					'deleted'		=>	$deleted,
					'display'		=>	$display,
					'checked'		=>	$checked,
					'newsletter'	=>	$newsletter,
				];
				$id = $this->repo->main->save($data);

				switch($method){
					case 1:
						return response()->json([
							'id'	=>	$id,
						]);
					break;
					default:
					case 0:
						return redirect()->route($this->route_index);
					break;
				}
            break;
        }

        $rows1 = [];
		$city = $this->repo->zip->city();

        if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['name'];

			$area = $this->repo->zip->area($rows1['city_id']);

			$rows1 = $this->repo->main->editor($rows1);
		}else{
			$city_id = $city[0]['id'];
			$area = $this->repo->zip->area($city_id);
		}
		

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
            'city'     	=>  $city,
            'area'     	=>  $area,
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
        $email = $request->input('email', null);
		$id = $request->input('id', null);
		
		switch($type){
			case 'email':
				echo $this->repo->main->validate($email, $id);
			break;
		}
    }

	public function search(Request $request)
	{
		$term = $request->get('term');

		$rows1 = $this->repo->main->search($term);

		$data = [
			'results'	=>	$rows1
		];
		return response()->json($data);
	}
}