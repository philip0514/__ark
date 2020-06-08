<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
	protected 	$repo;

	public function __construct()
	{
		$this->repo = new \stdClass();
    }
}
