<?php
namespace Philip0514\Ark\Repositories\Web;

use Philip0514\Ark\API\News;

class NewsRepository
{
	protected $page;

	function __construct()
	{
		$this->news = new News();
	}

	public function index($page, $limit)
	{
		$response = $this->news->index([
			'page'	=>	$page,
			'limit'	=>	$limit,
		]);

		$data = $response['data'];
		$html = $response['html'];
		$pagination = $response['pagination'];

		return [
			'data'			=>	$data,
			'html'			=>	$html,
			'pagination'	=>	$pagination,
		];
	}

	public function show($id)
	{
		$response = $this->news->show([
			'id'	=>	$id,
		]);

		$data = $response['data'];
		$html = $response['html'];

		return [
			'data'			=>	$data,
			'html'			=>	$html,
		];
	}
}