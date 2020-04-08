<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Dashboard\MailTemplateRepository as MainRepo;
use Philip0514\Ark\Repositories\Dashboard\MailTypeRepository;

class MailTemplateController extends Controller
{
    protected 	$repo, 
				$config,
				$path,
				$method = 'get',
				$route_index;

	function __construct(
        Request $request,
		MainRepo $main,
		MailTypeRepository $MailTypeRepository
	)
	{
		parent::__construct();
        $this->repo->main = $main;
        $this->repo->type = $MailTypeRepository;
        $this->method = strtolower($request->method());
        $this->path = $request->path();

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'郵件樣板',
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
					'name'			=>	'標題',
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
                $type = $request->input('type', null);
                $start_time = $request->input('start_time', null);
                $end_time = $request->input('end_time', null);
                $title = $request->input('title', null);
                $content = $request->input('content', null);
				$deleted = $request->input('deleted', 0);
				$display = $request->input('display', 0);
				$method = $request->input('__method', 0);

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'type'			=>	$type,
					'start_time'	=>	$start_time,
					'end_time'		=>	$end_time,
					'title'			=>	$title,
					'content'		=>	$content,
					'deleted'		=>	$deleted,
					'display'		=>	$display,
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

		/*
		$blade = 'Hello, {{ $planet }}!';
		$php = \Blade::compileString($blade);
		dd($this->render($php, ['planet' => 'World1']));
		*/

        $rows1 = [];
        if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['name'];

			$rows1 = $this->repo->main->editor($rows1);
		}

		$type = $this->repo->type->select()->where('display', 1)->orderBy('id', 'asc')->get()->toArray();

        $data = [
			'config'	=>	$this->config,
			'rows1'     =>  $rows1,
			'type'		=>	$type,
		];
        return $this->view($this->config['html']['single'], $data);
	}
	
	function render($__php, $__data)
	{
		$obLevel = ob_get_level();
		ob_start();
		extract($__data, EXTR_SKIP);
		try {
			eval('?' . '>' . $__php);
		} catch (Exception $e) {
			while (ob_get_level() > $obLevel) ob_end_clean();
			throw $e;
		} catch (Throwable $e) {
			while (ob_get_level() > $obLevel) ob_end_clean();
			throw new FatalThrowableError($e);
		}
		return ob_get_clean();
	}
}