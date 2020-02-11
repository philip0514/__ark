<?php
namespace Philip0514\Ark\Controllers;

use Auth;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;

//Repositories
use Philip0514\Ark\Repositories\SidebarRepository;

class Controller extends BaseController
{

	protected 	$repo;

	public function __construct()
	{
		$this->repo = new \stdClass();
		$this->repo->sidebar = new SidebarRepository();
	}

    protected function config()
    {
		$config = $this->config;

        $default = [
			'name'				=>	null,
			'route'				=>	null,
			'controller'		=>	null,
            'action'            =>  [
                'create'			=>	0,
				'update'			=>	0,
				'recommend'			=>	0,
				'softDelete'		=>	0,
				'delete'			=>	0,
				'display'			=>	0,
				'sort'				=>	0,
				'import'			=>	0,
				'export'			=>	0,
				'seark'			=>	0,
				'autocomplete'		=>	0,
            ],
			'column'			=>	[
				/*	
				array(
					'name'			=>	'#',
					'width'			=>	'60px',
					'field'			=>	'id',
					'visible'		=>	array(true, true),		//第一個：是否顯示在「欄位顯示」的select中 第二個：在select中是否已被勾選
					'orderby'		=>	array('A.id', 'asc'),
					'orderable'		=>	true,					//是否可以用th顯示排序
					'sortable'		=>	false,					//是否可以拖曳排序
				),	
				*/
			],
            'action_text'       =>  [
                'create'			=>	'新增',
				'update'			=>	'',
				'delete'			=>	'刪除',
				'display_on'		=>	'上架',
				'display_off'		=>	'下架',
				'sort'				=>	'',
				'import'			=>	'匯入',
				'export'			=>	'匯出',
				'seark'			=>	'',
            ],
            'path'				=>	[
				'index'				=>	$this->path('', 1),
				'single'			=>	$this->path('', 1),
                'datatable'			=>	$this->path('datatable', 1),
                'create'            =>  $this->path('create', 1),
                'validate'          =>  $this->path('validate', 1),
                'action'            =>  $this->path('action', 1),
				'columnVisible'     =>  $this->path('columnVisible', 1),
				'columnReorder'		=>	$this->path('columnReorder', 1),
				'rowReorder'		=>	$this->path('rowReorder', 1),
            ],
			'html'				=>	[
				'index'				=>	$this->path('index', 0),
				'list'				=>	$this->path('list', 0),
				'single'			=>	$this->path('single', 0),
				//'seark'			=>	$this->path($this->class_name, 'seark', 0),
				//'sort'				=>	$this->path($this->class_name, 'sort', 0),
            ],
			/*
			'seark'			=>	[
				'input'				=>	[],		//html input name
				'field'				=>	[],		//mysql field
				'value'				=>	[],		//已搜尋的值
			],
			*/
			'image'				=>	[],
			'table'				=>	[],
		];

		//seark
		$session = session()->get('admin');

		if(isset($session['datatable'][$config['controller']])){
			if(isset($session['datatable'][$config['controller']]['seark'])){
				$config['seark'] = $session['datatable'][$config['controller']]['seark'];
			}
			if(isset($session['datatable'][$config['controller']]['parameter'])){
				$config['parameter'] = $session['datatable'][$config['controller']]['parameter'];
			}
			if(isset($session['datatable'][$config['controller']]['columnVisible'])){
				$columnVisible = $session['datatable'][$config['controller']]['columnVisible'];
				for($i=0; $i<sizeof($config['column']); $i++){
					switch($config['column'][$i]['field']){
						case 'handler':
						case 'select_all':
						case 'update':
						break;
						default:
							if(in_array($i, $columnVisible)){
								$config['column'][$i]['visible'][1] = true;
							}else{
								$config['column'][$i]['visible'][1] = false;
							}
						break;
					}
				}
			}
		}

		$config = array_replace_recursive($default, $config);

		//permission
		if(!isset($config['route'])){
			return 0;
		}

		$permission = session()->get( config('ark.permission') );

		if(!$permission){
			return redirect()->route('login');
		}

		$method = [
			'create', 'update', 'delete'
		];
		for($i=0; $i<sizeof($method); $i++){
			$can = sprintf('%s %s', $method[$i], $config['controller']);
			$config['action'][ $method[$i] ] = (int)in_array($can, $permission);
		}

		$this->config = $config;
    }

    protected function datatable_config()
    {
		$config = $this->config;

		$table_view = [];

        //datatable config
		$datatable = array(
			'first'				    =>	'第一頁',
			'last'				    =>	'最終頁',
			'info'				    =>	' _START_ ~ _END_ / 共 _TOTAL_ 筆 ',
			'info_filtered'		    =>	'(全部 _MAX_ 筆)',
			'zero'				    =>	'無資料',
			'seark_placeholder'    =>	'敘述 必填',

            'action'			    =>	$config['action'],
            'path'                  =>  $config['path'],
			'columnDefs'		    =>	[
				[
					'orderable'		    =>	false, 
					'targets'		    =>	[]
                ],
            ],
			'colReorder'		    =>	[
				'fixedColumnsLeft'	    =>	2,
				'fixedColumnsRight' 	=>	1,
            ],
			'rowReorder'		    =>	false,
			'pageLength'		    =>	10,
			'displayStart'		    =>	0,
        );

		if($config['action']['sort']){
			$datatable['rowReorder'] = array(
				'selector'			=>	'td.sortable',
				'update'			=>	false,
			);
		}

		//解析datatable config
		for($i=0; $i<sizeof($config['column']); $i++){
			//資料與欄位的對應
			$datatable['columns'][] = array(
				'data'		=>	$config['column'][$i]['field'],
			);

			if(isset($config['column'][$i]['orderby'][1])){
				$datatable['order'][] = array($i, $config['column'][$i]['orderby'][1]);
			}

			//是否可以根據th排序
			if(!$config['column'][$i]['orderable']){
				$datatable['columnDefs'][0]['targets'][] = $i;
			}

			//th寬度
			if(isset($config['column'][$i]['width'])){
				$datatable['columnDefs'][] = array(
					'width'			=>	$config['column'][$i]['width'],
					'targets'		=>	$i,
				);
			}

			//visible 控制select中的顯示
			if($config['column'][$i]['visible'][0]){
                $is_visible = $config['column'][$i]['visible'][1];
				$datatable['visible'][] = array(
					'value'		=>	$i,
					'name'		=>	$config['column'][$i]['name'],
					'field'		=>	$config['column'][$i]['field'],
					'selected'	=>	$is_visible,
				);
            }

			//table顯示整理
			if($config['column'][$i]['name']=='select_all'){
				$table_view['thead'][] = '<div class="checkbox-custom checkbox-default">
					<input type="checkbox" id="select_all" value="1">
					<label></label>
				</div>';
			}else{
				$table_view['thead'][] = $config['column'][$i]['name'];
			}
        }

		$config['table_view'] = $table_view;

		$config['datatable'] = $datatable;

		$this->config = $config;
    }

	protected function path($url, $slash=1)
	{
        if($url=='list'){
            $path[] = 'dashboard';
        }else{
			$path[] = $this->config['controller'];
		}
		$path[] = $url;

		if($slash){
			return prefixUri(implode('/', $path));
		}else{
			return 'ark/'.implode('/', $path);
		}
    }

    protected function index(Request $request)
    {
		$this->permissionCheck();
		$this->datatable_config();
		$config = $this->config;

        $data = [
            'config'    =>  $config,
		];
        return $this->view($this->config['html']['list'], $data);
    }

	protected function datatable_extend($datatable, $raw_columns)
	{
		return [
			'datatable'		=>	$datatable,
			'raw_columns'	=>	$raw_columns,
		];
	}

    protected function datatable(Request $request)
    {
		$config = $this->config;
		$route = $config['route'];
		$path = prefixUri($config['controller']);
		$query = $this->repo->main->datatable($request);

		//確認是否為正序，是正序才可以排序
		$sortable = false;
		$order = $request->input('order', []);
		if(sizeof($order)==1){
			if(
				($config['column'][ $order[0]['column'] ]['field']=='sort' || $config['column'][ $order[0]['column'] ]['field']=='recommend_sort' )
				&& 
				$order[0]['dir']=='asc'
			){
				$sortable = true;
			}
		}

		$datatable = Datatables::of($query);

		//checkbox
		$raw_columns[] = 'select';
		if($config['action']['display'] || $config['action']['delete']){
			$datatable->addColumn('select', function($data){
				$id = $data->id;
				return sprintf('<div class="checkbox-custom checkbox-default">
					<input type="checkbox" class="row_select" value="%s">
					<input type="hidden" class="input-id" name="id[]" value="%s">
					<label></label>
				</div>', $id, $id);
			});
		}else{
			$datatable
			->addColumn('select', function(){
				return null;
			});
		}

		//update
		$raw_columns[] = 'update';
		if($config['action']['update']){
			$datatable
			->addColumn('update', function($data) use ($path){
				$id = $data->id;
				return sprintf('<a href="%s/%s" class="edit" title="編輯"><i class="fas fa-pen"></i></a>', $path, $id);
			});

			$raw_columns[] = 'name';
			$datatable->editColumn('name', function($data) use ($path){
				$id = $data->id;
				$name = $data->name;
				return sprintf('<a href="%s/%s" class="edit" title="%s">%s</a>', $path, $id, $name, $name);
			});
		}else{
			$datatable
			->addColumn('update', function(){
				return null;
			});
		}

		//sortable handler
		$raw_columns[] = 'handler';
		if($config['action']['sort']){
			$datatable
			->addColumn('handler', function(){
				return sprintf('<i class="fas fa-sort"></i>');
			});
		}else{
			$datatable
			->addColumn('sort', function(){
				return null;
			});
		}

		//sortable
		$datatable
			->addColumn('sortable', function() use ($sortable){
				return $sortable;
			});

		$datatable->editColumn('start_time', function($data){
			if($data->start_time){
				return date('Y-m-d H:i', $data->start_time);
			}
		});

		$datatable->editColumn('end_time', function($data){
			if($data->end_time){
				return date('Y-m-d H:i', $data->end_time);
			}
		});

		//recommend
		$raw_columns[] = 'recommend';
		$datatable->editColumn('recommend', function($data){
			if($data->recommend){
				return sprintf('<i class="fas fa-star"></i>');
			}
		});

		$result = $this->datatable_extend($datatable, $raw_columns);
		$datatable = $result['datatable'];
		$raw_columns = array_merge($raw_columns, $result['raw_columns']);

		return $datatable
			->rawColumns($raw_columns)
			->make(true);
    }

	protected function action(Request $request)
	{
        $type = $request->input('type', null);
		$id = $request->input('id', null);

		$this->repo->main->action($type, $id);
	}

	protected function columnVisible(Request $request)
	{
		$this->repo->main->columnVisible($request);
	}

	protected function rowReorder(Request $request)
	{
		$data = $request->input('data', null);
		if($data){
			$this->repo->main->rowReorder($data);
		}
	}

	public function columnReorder(Request $request)
	{
		$from = $request->input('from', null);
		$to = $request->input('to', null);
	}

	public function view($path, $data=null)
	{
		$data['sidebar'] = $this->repo->sidebar->init();
        return view($path, $data);
	}

	protected function permissionCheck()
	{
		$config = $this->config;
		list($controller, $method) = explode('.', $config['route']);
		switch($method){
			case 'index':
				$method = 'read';
			break;
		}

		$permission = session()->get( config('ark.permission') );
		$can = sprintf('%s %s', $method, $controller);

		$status = 1;
		switch($method){
			case 'create':
			case 'update':
			case 'read':
			case 'delete':
				$status = (int)in_array($can, $permission);
			break;
		}

		if(!$status){
			return redirect()->route('dashboard');
			exit;
		}
	}

	protected function columndef($rows1)
	{
		$rows2 = [
			'columnDefs'		=>	[
				[
					'orderable'		=>	false, 
					'targets'		=>	[]
				],
			],
		];

		for($i=0; $i<sizeof($rows1); $i++){
			$rows2['name'][] = $rows1[$i]['name'];

			$rows2['columns'][] = [
				'data'		=>	$rows1[$i]['field'],
			];

			if(isset($rows1[$i]['orderby'][1]) && $rows1[$i]['orderby'][1]){
				$rows2['order'][] = [$i, $rows1[$i]['orderby'][1]];
			}

			//是否可以根據th排序
			if(!isset($rows1[$i]['orderable']) || !$rows1[$i]['orderable']){
				$rows2['columnDefs'][0]['targets'][] = $i;
			}

			//th寬度
			if(isset($rows1[$i]['width']) && $rows1[$i]['width']){
				$rows2['columnDefs'][] = [
					'width'			=>	$rows1[$i]['width'],
					'targets'		=>	$i,
				];
			}
		}

		return $rows2;
	}
}