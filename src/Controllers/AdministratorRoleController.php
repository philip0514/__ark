<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\AdministratorRoleRepository as MainRepo;

class AdministratorRoleController extends Controller
{
    protected 	$repo, 
				$config,
				$method = 'get',
				$route_index = 'administratorRole.index';

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

        $this->config  = [
			'name'				=>	'角色',
			'route'				=>	$route,
			'controller'		=>	$controller,
			'action'			=>	[
				'create'			=>	1,
				'update'			=>	1,
				'delete'			=>	1,
				'display'			=>	1,
				'sort'				=>	0,
				'import'			=>	0,
				'export'			=>	0,
				'search'			=>	1,
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
					'field'			=>	'created_at',
					'visible'		=>	[true, true],
					'orderby'		=>	['created_at'],
					'orderable'		=>	true,
					'sortable'		=>	false,
                ],
				[
					'name'			=>	'更新時間',
					'field'			=>	'updated_at',
					'visible'		=>	[true, true],
					'orderby'		=>	['updated_at'],
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
				$name = $request->input('name');
				$display = $request->input('display', 0);
				$id = $request->input('id', 0);
				$permission = $structure_id = [];

				$structure = $this->repo->main->structure();
				for($i=0; $i<sizeof($structure); $i++){
					$p = $request->input('permission-'.$structure[$i]['id']);
					if($p){
						$permission[ $structure[$i]['id'] ] = $p;
						if($structure[$i]['parent_id'] && !in_array($structure[$i]['parent_id'], $structure_id)){
							$structure_id[] = $structure[$i]['parent_id'];
						}
						if(!in_array($structure[$i]['id'], $structure_id)){
							$structure_id[] = $structure[$i]['id'];
						}
					}
				}
				
				asort($structure_id);
				$structure_id = array_values($structure_id);

				$data = [
					'id'			=>	$id,
					'name'			=>	$name,
					'display'		=>	$display,
					'structure'		=>	$structure_id ? json_encode($structure_id, JSON_NUMERIC_CHECK) : null,
					'permission'	=>	$permission,
				];

                if(!$id){
                    $this->repo->main->create($data);
                }else{
                    $this->repo->main->update($data);
                }
                return redirect()->route($this->route_index);

				exit;
			break;
        }

		$rows1 = $rows3 = [];
		if($id){
            $rows1 = $this->repo->main->single($id);
            if(!$rows1){
                return redirect()->route($this->route_index);
			}

			$this->config['name'] = $rows1['name'];

			$rows3 = $this->repo->main->getRolePermissions($id);
		}
		
		$rows2 = $this->repo->main->structure();
		$rows2 = $this->structure_html($rows1, $rows2, $rows3);


        $data = [
			'config'	=>	$this->config,
			'rows1'     =>  $rows1,
			'rows2'		=>	$rows2,
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
        $name = $request->input('name', null);
        $id = $request->input('id', null);
		
		switch($type){
			case 'name':
				echo $this->repo->main->validate($name, $id);
			break;
		}
    }
	
	/**
	 * structure_html
	 *
	 * @param [type] $rows1		role
	 * @param [type] $rows2		structure
	 * @param [type] $permission
	 * @return html
	 */
	private function structure_html($rows1, $rows2, $permission=[])
	{
		$dom = new \DOMDocument("1.0");
		$dom->formatOutput = true;
		$node = $dom->createElement("ul");
		$node->setAttribute("class", 'permission-list list-icons');
		$parent_node = $dom->appendChild($node);
		$structure = [];
		if(isset($rows1['structure'])){
			$structure = json_decode($rows1['structure'], true);
		}

		for($i=0; $i<sizeof($rows2); $i++){
			$id = $rows2[$i]['id'];
			$parent_id = $rows2[$i]['parent_id'];
			$parent = $parent_node;
			$method = json_decode($rows2[$i]['method'], true);

			$node_ol[$parent_id] = null;

			if($parent_id){
				if(!isset($node_ol[$parent_id])){
					$node_ol[$parent_id] = $dom->createElement("ul");						//start ol
				}
				$parent = $node_ol[$parent_id];
			}
			
			$nodes[$id] = $dom->createElement("li");								//start li
			//$nodes[$id]->setAttribute("id", 'list_'.($i+1));						//set li attr
			$nodes[$id]->setAttribute("style", 'font-size:15px;');
			$nodes[$id]->setAttribute("class", 'text-primary');
			/*
			$checkbox = $dom->createElement('div');
			$checkbox->setAttribute('class', 'checkbox-custom checkbox-primary');

				$checkbox_input = $dom->createElement('input');
				$checkbox_input->setAttribute('id', 'structure'.$id);
				$checkbox_input->setAttribute('type', 'checkbox');
				$checkbox_input->setAttribute('name', 'structure[]');
				$checkbox_input->setAttribute('class', 'structure');
				$checkbox_input->setAttribute('value', $id);
				if(in_array($rows2[$i]['id'], $structure)){
					$checkbox_input->setAttribute('checked', 'checked');
				}
			$checkbox->appendChild($checkbox_input);

				$checkbox_label = $dom->createElement('label', ' '.$rows2[$i]['name']);
				$checkbox_label->setAttribute('for', 'structure'.$id);

			$checkbox->appendChild($checkbox_label);
			$nodes[$id]->appendChild($checkbox);
			*/

			if($rows2[$i]['icon']){
				$icon = $dom->createElement('i');
				$icon->setAttribute('class', 'm-r-10 m-t-5 fas '.$rows2[$i]['icon']);
				$nodes[$id]->appendChild($icon);
			}

			$name = $dom->createElement('div', $rows2[$i]['name']);
			$name->setAttribute('class', 'p-b-10');
			$nodes[$id]->appendChild($name);

			if($method){
				$div = $dom->createElement('div');

				$select = $dom->createElement('select');
				$select->setAttribute('name', 'permission-'.$rows2[$i]['id'].'[]');
				if($rows2[$i]['parent_id']){
					$select->setAttribute('class', 'selectpicker p-b-10');
				}else{
					$select->setAttribute('class', 'selectpicker p-b-10 p-l-25');
				}
				$select->setAttribute('multiple', 'multiple');
				$select->setAttribute('data-actions-box', 'true');

				if(in_array('read', $method)){
					$option = $dom->createElement('option', '讀取');
					$option->setAttribute('value', 'read');
					if(isset($permission[ $rows2[$i]['url'] ]) && in_array('read', $permission[ $rows2[$i]['url'] ])){
						$option->setAttribute('selected', 'selected');
					}
					$select->appendChild($option);
				}

				if(in_array('create', $method)){
					$option = $dom->createElement('option', '新增');
					$option->setAttribute('value', 'create');
					if(isset($permission[ $rows2[$i]['url'] ]) && in_array('create', $permission[ $rows2[$i]['url'] ])){
						$option->setAttribute('selected', 'selected');
					}
					$select->appendChild($option);
				}

				if(in_array('update', $method)){
					$option = $dom->createElement('option', '修改');
					$option->setAttribute('value', 'update');
					if(isset($permission[ $rows2[$i]['url'] ]) && in_array('update', $permission[ $rows2[$i]['url'] ])){
						$option->setAttribute('selected', 'selected');
					}
					$select->appendChild($option);
				}

				if(in_array('delete', $method)){
					$option = $dom->createElement('option', '刪除');
					$option->setAttribute('value', 'delete');
					if(isset($permission[ $rows2[$i]['url'] ]) && in_array('delete', $permission[ $rows2[$i]['url'] ])){
						$option->setAttribute('selected', 'selected');
					}
					$select->appendChild($option);
				}

				$div->appendChild($select);
				$nodes[$id]->appendChild($div);
			}

			$parent->appendChild($nodes[$id]);										//end li

			if($parent_id){
				$nodes[$parent_id]->appendChild($parent);									//end ol
			}
		}
		$html = $dom->saveHTML();

		return $html;
	}
}