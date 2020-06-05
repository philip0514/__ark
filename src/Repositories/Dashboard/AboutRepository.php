<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Philip0514\Ark\Models\About as Model;

class AboutRepository extends Repository
{
    protected $model;

	function __construct(
        Model $model,
        PageBlockRepository $PageBlockRepository
	){
		parent::__construct();
        $this->model = $model;
        $this->block = $PageBlockRepository;
    }

    public function single($id)
    {
		$rows1 = $this->model
			->checkTrashed()
			->with([
				'ogimages'	=>	function($query){
					$query->orderBy('sort', 'asc');
				}
			])
			->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }

    public function save($data)
    {
        $id = isset($data['id']) ? (int)$data['id'] : 0;
        unset($data['id']);

        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $data['deleted_by'] = null;
        if($deleted){
            $data['display'] = 0;
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
				'type'		=>	'ogimage',
			];
        }

        switch($id){
			default:
			case 0:
			case null:
                //insert
                $data = $this->_create($data);
                $id = $this->model->insertGetId($data);
            break;
            case $id:
                //update
                $data = $this->_update($data);

                if($deleted){
					$data['deleted_by'] = $data['updated_by'];
				}else{
                    $this->restore($id);
                }

                $this->model
                ->checkTrashed()
                ->where('id', $id)
                ->update($data);
            break;
        }

		$rows1 = $this->model->checkTrashed()->find($id);
		$rows1->ogimages()->sync($ogimage);

        if($deleted){
            $this->delete($id);
        }

        return $id;
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