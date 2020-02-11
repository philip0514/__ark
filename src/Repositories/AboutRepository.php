<?php
namespace Philip0514\Ark\Repositories;

use Philip0514\Ark\Repositories\Repository;

use Philip0514\Ark\Models\About as Model;

class AboutRepository extends Repository
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
			->with([
				'media'	=>	function($query){
					$query->orderBy('sort', 'asc');
				}
			])
			->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }

    public function create($data)
    {
		$data = $this->_create($data);

		unset($data['id']);

        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $data['deleted_by'] = null;
        if($deleted){
            $data['deleted_by'] = $data['updated_by'];
        }

		$ogimage_input = explode(',',$data['ogimage_input']);
		unset($data['ogimage_input']);
		$ogimage = [];
		for($i=0; $i<sizeof($ogimage_input); $i++){
			if(!$ogimage_input[$i]){
				continue;
			}
			$ogimage[ $ogimage_input[$i] ] = [
				'sort'		=>	$i,
			];
		}

		$id = $this->model->insertGetId($data);

		$rows1 = $this->model->checkTrashed()->find($id);
		$rows1->media()->sync($ogimage);

        if($deleted){
            $this->delete($id);
        }

        return $id;
    }

    public function update($data)
    {
		$data = $this->_update($data);

		$id = $data['id'];
		unset($data['id']);

        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $data['deleted_by'] = null;
        if($deleted){
            $data['display'] = 0;
            $data['deleted_by'] = $data['updated_by'];
        }else{
            $this->restore($id);
        }

		$ogimage_input = explode(',',$data['ogimage_input']);
		unset($data['ogimage_input']);
		$ogimage = [];
		for($i=0; $i<sizeof($ogimage_input); $i++){
			if(!$ogimage_input[$i]){
				continue;
			}
			$ogimage[ $ogimage_input[$i] ] = [
				'sort'		=>	$i,
			];
		}

        $this->model
			->checkTrashed()
            ->where('id', $id)
			->update($data);

		$rows1 = $this->model->checkTrashed()->find($id);
		$rows1->media()->sync($ogimage);

        if($deleted){
            $this->delete($id);
        }
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