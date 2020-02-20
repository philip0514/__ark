<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Philip0514\Ark\Models\Setting as Model;
use Philip0514\Ark\Models\OauthClient;

class SettingRepository extends Repository
{
    protected $model;

	function __construct(
		Model $model,
		OauthClient $OauthClient
	){
		$this->model = $model;
		$this->client = $OauthClient;
	}

    public function single($id)
    {
		$rows1 = $this->model->checkTrashed()
		->with([
			'ogimages'	=>	function($query){
				$query->orderBy('sort', 'asc');
			},
			'tags'	=>  function($query){
				$query->select('*', 'name as text');
				$query->orderBy('sort', 'asc');
				$query->orderBy('tags.id', 'asc');
			},
		])
		->find($id);

		$rows2 = $this->client->orderBy('id', 'asc')->get();

        if($rows1){
			$rows1 = $rows1->toArray();
        }

		$rows1['client'] = $rows2->toArray();

		return $rows1;
    }
	
	public function update($data)
	{
		$id = $data['id'];
		unset($data['id']);
		$rows1 = $this->model->find($id);

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

		$tag = $data['tag'];
		unset($data['tag']);

		if(!$rows1){
			$id = $this->model->insertGetId($data);
			$rows1 = $this->model->checkTrashed()->find($id);
		}else{
			$this->model
				->where('id', $id)
				->update($data);
		}

		$rows1->ogimages()->sync($ogimage);
		$rows1->tags()->sync($tag);
	}
}