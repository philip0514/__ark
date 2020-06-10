<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Philip0514\Ark\Models\PageBlock;
use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;
use Philip0514\Ark\Models\Setting;

use Philip0514\Ark\Serializer\API\V1_0\PageSerializer;

class PageRepository
{
    public $spliter, $meta;

	function __construct()
	{
        $this->spliter = config('ark.spliter');
		$setting = new Setting();
		$serializer = new PageSerializer();

		$site = $setting->with([
			'ogimages'	=>	function($query){
                $query->orderBy('sort', 'asc');
            },
			'tags',
		])->find(1)->toArray();

		$this->meta = $serializer->siteMeta($site);
	}

	private function getHeaderFooter()
	{
		$pageBlock = new PageBlock();

		$header = $footer = null;
		$rows1 = $pageBlock->whereIn('key', ['header', 'footer'])->orderBy('id', 'desc')->get()->toArray();
		for($i=0; $i<sizeof($rows1); $i++){
			switch($rows1[$i]['key']){
				case 'header':
					$header = $rows1[$i]['html'];
				break;
				case 'footer':
					$footer = $rows1[$i]['html'];
				break;
			}
		}

		return [
			'header'	=>	$header,
			'footer'	=>	$footer,
		];
	}

    public function get($url=null)
    {
		$serializer = new PageSerializer();
		$pageType = new PageType();
		$page = new Page();

		$rows1 = $pageType
			->where('slug', $url)
			->first();

		$rows2 = $page
			->with([
				'ogimages',
				'tags'
			])
			->where('display', 1);
		if($rows1){
			$rows1 = $rows1->toArray();
			$rows2 = $rows2->where('type', $rows1['id'])->first();
		}else{
			$rows2 = $rows2->where('url', '/'.$url)->first();
		}

		if($rows2){
			$rows2 = $rows2->toArray();
		}else{
            return null;
            
            //404
		}

		$meta = $serializer->meta($rows2, $this->meta);

		$headerFooter = $this->getHeaderFooter();

		return [
			'meta'		=>	$meta,
			'header'	=>	$headerFooter['header'],
			'footer'	=>	$headerFooter['footer'],
			'html'		=>	$rows2['html'],
			'css'		=>	$rows2['css'],
		];
    }
}
