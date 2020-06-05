<?php
namespace Philip0514\Ark\Repositories\Web;

use Philip0514\Ark\Models\PageBlock;
use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;

class PageBlockRepository
{
	public function get($type)
	{
		$pageBlock = new PageBlock();
		$pageType = new PageType();
		$page = new Page();

		switch($type){
			case 'header':
				$rows1 = $pageBlock->where('key', 'header')->orderBy('id', 'asc')->first();
				$html = null;
				if($rows1 && $rows1->html){
					$html = $rows1->html;
				}
				$data = [
					'html'	=>	$html,
				];
			break;
			case 'footer':
				$rows1 = $pageBlock->where('key', 'footer')->orderBy('id', 'asc')->first();
				$html = null;
				if($rows1 && $rows1->html){
					$html = $rows1->html;
				}
				$data = [
					'html'	=>	$html,
				];
			break;
			default:
				$rows1 = $pageType->where('slug', $type)->first()->toArray();
				$rows2 = $page->where('type', $rows1['id'])->first()->toArray();

				$data = [
					'title'			=>	$rows2['title'],
					'description'	=>	$rows2['description'],
					'html'			=>	$rows2['html'],
					'css'			=>	$rows2['css'],
				];
			break;
		}

		return $data;

	}
}