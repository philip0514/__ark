<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Auth;
use Philip0514\Ark\Models\Structure;
use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Permission;

use Philip0514\Ark\Traits\Helper;

class SidebarRepository
{
    use Helper;

    protected $model;

    function __construct()
    {
		$this->structure = new Structure();
		$this->role = new Role();
		$this->permission = new Permission();
    }

    public function init()
    {
        $admin = Auth::guard('admin')->user();
        $roles = $admin->getRoleNames()->toArray();

        $rows1 = $this->role->whereIn('name', $roles)->where('display', 1)->get()->toArray();
        $structure_id = [];
        for($i=0; $i<sizeof($rows1); $i++){
            $structure = json_decode($rows1[$i]['structure'], true);
            for($j=0; $j<sizeof($structure); $j++){
                if(!in_array($structure[$j], $structure_id)){
                    $structure_id[] = $structure[$j];
                }
            }
        }

        $rows2 = $this->structure->whereIn('id', $structure_id)->where('display', 1)->orderBy('sort', 'asc')->get()->toArray();

        return $this->html($rows2);
    }

    public function html($rows1)
    {
        $node = [];

		$dom = new \DOMDocument("1.0");
		$dom->formatOutput = true;
            $ul = $dom->createElement("ul");
            $ul->setAttribute("class", 'site-menu');
            $ul->setAttribute("data-plugin", 'menu');

        //分隔線
        $root = $dom->appendChild($ul);
            $li = $dom->createElement("li");
            $li->setAttribute("class", 'site-menu-category');

        /*
        //主控台
        $root->appendChild($li);
            $li = $dom->createElement("li");
            $li->setAttribute("class", 'site-menu-item p-v-3');
                $link = $dom->createElement("a");
                $link->setAttribute("class", 'animsition-link');
                $link->setAttribute("href", prefixUri('/'));
                    $icon = $dom->createElement("i");
                    $icon->setAttribute("class", 'site-menu-icon fas fa-tachometer-alt');
                    $icon->setAttribute("aria-hidden", 'true');
                    $span = $dom->createElement("span", '主控台');
                    $span->setAttribute("class", 'site-menu-title');
                $link->appendChild($icon);
                $link->appendChild($span);
            $li->appendChild($link);
        $root->appendChild($li);
        */

        $node_parent = [];
        for($i=0; $i<sizeof($rows1); $i++){

			$id = $rows1[$i]['id'];
            $parent_id = $rows1[$i]['parent_id'];
            $has_sub = 0;
            if(isset($rows1[$i+1]['parent_id']) && $rows1[$i+1]['parent_id']==$rows1[$i]['id']){
                $has_sub = 1;
            }

			$parent = $root;			//預設放在root中

			if($parent_id){
				//若有父層，先判斷是否已創造，則將parent 設為新結點
				if(!isset($node_parent[$parent_id])){
					$node_parent[$parent_id] = $dom->createElement("ul");
					$node_parent[$parent_id]->setAttribute("class", 'site-menu-sub');
				}
				$parent = $node_parent[$parent_id];
			}

            //li
            $class_name = [];
            $node[$id] = $dom->createElement("li");

            //若有子項目
			if($has_sub){
                $node[$id]->setAttribute("class", 'site-menu-item has-sub p-v-3');
            }else{
                $node[$id]->setAttribute("class", 'site-menu-item p-v-3');
            }

			//li > a
			$link = $dom->createElement('a');

            $url = $rows1[$i]['url'];
            if($url=='dashboard'){
                $url = '/';
            }
			if(!$url){
				$link->setAttribute("href", 'javascript:;');
			}else{
				$link->setAttribute("href", prefixUri($url));
				$link->setAttribute("class", 'animsition-link');
			}

			//li > a > i.icon
			if(substr($rows1[$i]['icon'], 0, 3)=='fa-' && !$rows1[$i]['parent_id']){
				$icon = $dom->createElement('i');
				$icon->setAttribute("class", 'site-menu-icon fas '.$rows1[$i]['icon']);
				$link->appendChild($icon);
			}

			//li > a > span.name
			$span = $dom->createElement('span', $rows1[$i]['name']);
			$span->setAttribute("class", 'site-menu-title');
			$link->appendChild($span);

			//若有子項目
			if($has_sub){
				$span_icon = $dom->createElement('span');
				$span_icon->setAttribute("class", 'site-menu-arrow');
				$link->appendChild($span_icon);
				$link->setAttribute('class', 'animsition-link');
				$link->setAttribute("href", 'javascript:;');
			}

			$node[$id]->appendChild($link);	

			$parent->appendChild($node[$id]);

			if($parent_id){
				$node[$parent_id]->appendChild($parent);
			}
        }

        return $dom->saveHTML();
    }
}
?>