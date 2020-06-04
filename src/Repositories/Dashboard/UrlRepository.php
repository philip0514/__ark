<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Models\News;

class UrlRepository
{
    protected $model;

	function __construct(
		News $news
	){
		$this->news = $news;
	}
	
	public function news()
	{
		$rows1 = $this->news
		->where('display', 1)
		->orderBy('id', 'desc')
		->get()
		->toArray();

		return $rows1;
	}
}