<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Philip0514\Ark\Models\Mail as Model;

class MailRepository extends Repository
{
    protected $model;

	function __construct(
		Model $model
	){
		parent::__construct();
		$this->model = $model;
    }

    public function single($id)
    {
		$rows1 = $this->model
			->checkTrashed()
			->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }
}