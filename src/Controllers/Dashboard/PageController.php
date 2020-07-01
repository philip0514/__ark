<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Storage;
use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Dashboard\PageRepository as MainRepo;
use Philip0514\Ark\Repositories\Dashboard\PageTypeRepository;
use Philip0514\Ark\Repositories\Dashboard\PageBlockRepository;

//Serializer
use Philip0514\Ark\Serializer\MediaSerializer;

use Philip0514\Ark\Media;

class PageController extends Controller
{
    protected 	$repo, 
				$config,
				$path,
				$method = 'get',
				$route_index;

	function __construct(
        Request $request,
		MainRepo $main,
		PageTypeRepository $PageTypeRepository,
		PageBlockRepository $PageBlockRepository
	)
	{
		parent::__construct();
		$this->repo->main = $main;
		$this->repo->pageType = $PageTypeRepository;
        $this->repo->block = $PageBlockRepository;
        $this->method = strtolower($request->method());
		$this->path = $request->path();

        if(!request()->route()){
            return false;
        }

		$route = $request->route()->getName();
		list($controller, $name) = explode('.', $route);
		$this->route_index = sprintf('%s.index', $controller);

        $this->config  = [
			'name'				=>	'頁面',
			'route'				=>	$route,
			'controller'		=>	$controller,
			'action'			=>	[
				'create'			=>	1,
				'update'			=>	1,
				'delete'			=>	0,
				'softDelete'		=>	0,
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
                $url = $request->input('url', null);
                $start_time = $request->input('start_time', null);
                $end_time = $request->input('end_time', null);
                $title = $request->input('title', null);
                $description = $request->input('description', null);
                $content = $request->input('content', null);
                $html = $request->input('htmlContent', null);
                $css = str_replace('"', "'", $request->input('cssContent', null));
                $json = $request->input('jsonContent', null);
				$display = $request->input('display', 0);
				$method = $request->input('__method', 0);
				$ogimage_input = $request->input('ogimage_input', 0);
				$tag = $request->input('tag', 0);

				$block = $this->repo->block->parse($html, $json);
				$html = $block['html'];
				$json = $block['json'];

				if($type!=1){
					$rows2 = $this->repo->pageType->select()->where('id', $type)->orderBy('id', 'asc')->first()->toArray();
					$url = $rows2['url'];
				}

				if(substr($url, 0, 1)!='/'){
					$url = '/'.$url;
				}

				/*
				if(substr($url, -1)!='/'){
					$url .= '/';
				}
				*/

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'type'			=>	$type,
					'url'			=>	$url,
					'start_time'	=>	$start_time,
					'end_time'		=>	$end_time,
					'title'			=>	$title,
					'description'	=>	$description,
					'content'		=>	$content,
					'html'			=>	$html,
					'css'			=>	$css,
					'json'			=>	$json,
					'display'		=>	$display,
					'ogimage_input'	=>	$ogimage_input,
					'tag'			=>	$tag,
				];
				$id = $this->repo->main->save($data);

				$rows1 = $this->repo->main->single($id);
				$serializer = new MediaSerializer();
				$media = $serializer->path($rows1['ogimages'], 'facebook');
				$ogimage = [];
				for($i=0; $i<sizeof($media); $i++){
					$ogimage[] = $media[$i]['path'];
				}
				$data = [
					'title'				=>	$title,
					'description'		=>	$description,
					'content'			=>	$content,
					'og'				=>	[
						'image'				=>	$ogimage,
					],
				];

				if(isset($rows2)){
					if(config('ark.media.s3.active')){
						$disk = Storage::disk('s3');
					}else{
						$disk = Storage::disk('public');
					}
					$remote = sprintf('page/%s.json', $rows2['slug']);
					$disk->put($remote, json_encode($data, JSON_UNESCAPED_UNICODE));
				}

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

        $rows1 = $tag = [];
		$html = $json = null;
        if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['name'];
			$html = $rows1['html'];
			$json = $rows1['json'];

			$media = new Media();
			$rows2 = $media->integrate($rows1['ogimages'], 'facebook');
			$rows1['ogimage_input'] = $rows2['id'];
			$rows1['ogimage_data'] = $rows2['data'];

			$tag = $rows1['tags'];

			$rows1 = $this->repo->main->editor($rows1);
		}
		$type = $this->repo->pageType->select()->orderBy('sort', 'asc')->get()->toArray();

		$block = $this->repo->block->merge($html, $json);
		$rows1['html'] = $block['html'];
		$rows1['json'] = $block['json'];

        $data = [
			'config'	=>	$this->config,
			'rows1'     =>  $rows1,
			'type'		=>	$type,
            'tag'     	=>  $tag,
		];
        return $this->view($this->config['html']['single'], $data);
    }
}