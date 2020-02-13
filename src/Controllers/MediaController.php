<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\MediaRepository as MainRepo;

//Serializer
use Philip0514\Ark\Serializer\MediaSerializer;

//trait
use Philip0514\Ark\Traits\Helper;

class MediaController extends Controller
{
	use Helper;

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

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'媒體',
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
			'html'				=>[
				'list'				=>	'ark::media.list',
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
					'name'			=>	'圖片',
					'width'			=>	'70px',
					'field'			=>	'image',
					'visible'		=>	[true, true],
					'orderby'		=>	['image'],
					'orderable'		=>	false,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'名稱',
					'field'			=>	'title',
					'visible'		=>	[true, true],
					'orderby'		=>	['title'],
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
            ]
        ];

		$this->config();
    }

    public function single(Request $request, $id=null)
    {
		$this->permissionCheck();

        switch($this->method){
            case 'post':
				$id = $request->input('id', 0);
                $title = $request->input('title');
                $description = $request->input('description', null);
                $cropper_data = $request->input('cropper_data', null);
				$custom_crop = $request->input('custom_crop', 0);
				$deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$tag = $request->input('tag', 0);
				$method = $request->input('__method', 0);

				for($i=0; $i<sizeof($cropper_data); $i++){
					$cropper_data[$i] = json_decode($cropper_data[$i], true);
				}

				$data = [
					'id'			=>	$id,
					'title'			=>	$title,
					'description'	=>	$description,
					'crop_data'		=>	json_encode($cropper_data),
					'custom_crop'	=>	$custom_crop,
					'deleted'		=>	$deleted,
					'display'		=>	$display,
					'tag'			=>	$tag,
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

        $rows1 = $tag = [];
        if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['title'];

			list($month, $t) = explode('-', $rows1['name']);
			$rows1['month'] = date('Ym', $month);
			$rows1['crop_data'] = json_decode($rows1['crop_data'], true);

			$tag = $rows1['tags'];

			$rows1 = $this->repo->main->editor($rows1);
		}

        $data = [
			'config'	=>	$this->config,
            'rows1'     =>  $rows1,
            'tag'     	=>  $tag,
        ];
        return $this->view($this->config['html']['single'], $data);
	}
	
	public function upload(Request $request)
	{
		$result = $this->repo->main->upload($request);

		echo json_encode($result);
	}
	
	protected function datatableExtend($datatable, $raw_columns)
	{
		$raw_columns[] = 'image';
		$datatable
			->addColumn('image', function($data){
				$path = $this->mediaPath($data->name);
				return sprintf('<img src="%s" class="rounded" style="width:60px;" />', $path);
			});

		return [
			'datatable'		=>	$datatable,
			'raw_columns'	=>	$raw_columns,
		];
	}
	
	public function manager(Request $request)
	{
        return view('ark::media.manager');
	}

	public function data(Request $request)
	{
		$limit = 40;
		$page 	= $request->input('page', 1);
		$search 	= $request->input('search', null);
		$skip 	= $request->input('skip', null);
		$request_time 	= $request->input('request_time', time());

		$rows1 = $this->repo->main->data($page, $limit, $request_time, $skip, $search);

		$serializer = new MediaSerializer();
		$result = $serializer->data($rows1);

		echo json_encode($result);
	}

	public function editor(Request $request)
	{
        switch($this->method){
			case 'post':
				$media_id = $request->input('media_id', 0);
				$custom_crop = $request->input('custom_crop', 0);
				$cropper_data = $request->input('cropper_data', []);

				for($i=0; $i<sizeof($cropper_data); $i++){
					$cropper_data[$i] = json_decode($cropper_data[$i], true);
				}

				$data = [
					'id'			=>	$media_id,
					'crop_data'		=>	json_encode($cropper_data),
					'custom_crop'	=>	$custom_crop,
				];
				$result = $this->repo->main->update($data);

				echo json_encode($result);

				exit;
			break;
		}

		$id = $request->input('id', 0);
		$rows1 = $this->repo->main->single($id);

		list($month, $t) = explode('-', $rows1['name']);
		$rows1['month'] = date('Ym', $month);
		$rows1['crop_data'] = json_decode($rows1['crop_data'], true);

		$data = [
			'rows1'		=>	$rows1,
		];
		return view('ark::media.editor', $data);
	}
}