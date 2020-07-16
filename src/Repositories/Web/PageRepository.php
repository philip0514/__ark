<?php
namespace Philip0514\Ark\Repositories\Web;

use Philip0514\Ark\API\Page;
/*
use Philip0514\Ark\Models\PageBlock;
use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;
use Philip0514\Ark\Models\Setting;

use Philip0514\Ark\Serializer\Web\PageSerializer;
*/

class PageRepository
{
	protected $page;

	function __construct()
	{
		$this->page = new Page();
	}

	public function get($url)
	{
		$response = $this->page->get([
			'url'	=>	$url
		]);

		$data = $response['data'];
		$html = $response['html'];

		return [
			'data'		=>	$data,
			'html'		=>	$html,
		];
	}
}