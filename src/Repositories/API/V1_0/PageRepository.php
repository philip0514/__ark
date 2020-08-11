<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Philip0514\Ark\Models\PageBlock;
use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;
use Philip0514\Ark\Models\Setting;

use Philip0514\Ark\Serializer\API\V1_0\PageSerializer;

use Sunra\PhpSimple\HtmlDomParser;

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

	public function getHeaderFooter($user_id=0)
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

		if($header){
			if($user_id){
				$dom = HtmlDomParser::str_get_html($header);
				$user = $dom->find('.nav-item-user', 0);
				$user->class .= ' dropdown';
				$user->innertext .= sprintf('<ul class="dropdown-menu">
					<li><a class="dropdown-item" href="%s">My Account</a></li>
					<li><a class="dropdown-item" href="%s">Orders</a></li>
					<div class="dropdown-divider"></div>
					<li><a class="dropdown-item" href="%s">Logout</a></li>
				</ul>',
					route('user_info'),
					route('user_order'),
					route('logout')
				);

				$header = $dom->outertext;
			}
		}

		return [
			'header'	=>	$header,
			'footer'	=>	$footer,
		];
	}

    public function get($url=null, $user_id=0)
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

		$headerFooter = $this->getHeaderFooter($user_id);

		//$html = html_entity_decode($rows2['html'], ENT_QUOTES);
        //$php = Blade::compileString($html);
		//$rows2['html'] = $this->renderHtml($php);

		return [
			'meta'		=>	$meta,
			'header'	=>	$headerFooter['header'],
			'footer'	=>	$headerFooter['footer'],
			'body'		=>	$rows2['html'],
			'css'		=>	$rows2['css'],
		];
	}

	public function meta($meta)
	{
		$serializer = new PageSerializer();

		$meta = $serializer->meta($meta, $this->meta);

		return $meta;
	}
}
