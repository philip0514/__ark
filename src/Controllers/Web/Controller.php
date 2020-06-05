<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

//Repositories
use Philip0514\Ark\Repositories\Web\PageBlockRepository;

class Controller extends BaseController
{

	protected 	$repo;

	public function __construct()
	{
		$this->repo = new \stdClass();
		$this->repo->block = new PageBlockRepository();
    }

	public function view($path, $data=null)
	{
		$header = $this->repo->block->get('header');
        $footer = $this->repo->block->get('footer');

        $data['header'] = $header['html'];
        $data['footer'] = $footer['html'];

        return view($path, $data);
	}
}
