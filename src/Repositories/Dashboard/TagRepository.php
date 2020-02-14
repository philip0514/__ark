<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Philip0514\Ark\Models\Tag as Model;

class TagRepository extends Repository
{
    protected $model;

	function __construct(
		Model $model
	){
		parent::__construct();
		$this->model = $model;
	}

	public function search($text)
	{
		$rows1 = $this->model->select('id', 'name as text')->where('name', 'like', '%'.$text.'%')->get()->toArray();
		return $rows1;
	}

    protected function actionDeleteForce($id)
    {
		$rows1 = $this->model->whereIn('id', $id)->get();
		for($i=0; $i<sizeof($rows1); $i++){
			$this->model->detachAll($rows1[$i]);
		}
        $this->model->whereIn('id', $id)->forceDelete();
    }
}