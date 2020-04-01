<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Dashboard\NewsRepository as MainRepo;

use Philip0514\Ark\Media;

class NewsController extends Controller
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
			'name'				=>	'最新消息',
			'route'				=>	$route,
			'controller'		=>	$controller,
			'action'			=>	[
				'create'			=>	1,
				'update'			=>	1,
				'softDelete'		=>	1,
				'delete'			=>	1,
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
                $name = $request->input('name');
                $news_time = strtotime( $request->input('news_time', date('Y-m-d H:i:s', time()) ) );
                $description = $request->input('description', null);
                $content = $request->input('content', null);
				$deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$method = $request->input('__method', 0);
				$ogimage_input = $request->input('ogimage_input', 0);

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'news_time'		=>	$news_time,
					'description'	=>	$description,
					'content'		=>	$content,
					'ogimage_input'	=>	$ogimage_input,
					'deleted'		=>	$deleted,
					'display'		=>	$display,
				];
				$id = $this->repo->main->save($data);

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

			$media = new Media();
			$rows2 = $media->integrate($rows1['ogimages'], 'facebook');
			$rows1['ogimage_input'] = $rows2['id'];
			$rows1['ogimage_data'] = $rows2['data'];
		}

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
		];
        return $this->view($this->config['html']['single'], $data);
    }
}