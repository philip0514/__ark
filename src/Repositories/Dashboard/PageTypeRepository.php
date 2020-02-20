<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Philip0514\Ark\Models\PageType as Model;

class PageTypeRepository extends Repository
{
    protected $model;

	function __construct(
		Model $model
	){
		parent::__construct();
		$this->model = $model;
    }
}