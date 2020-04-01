<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Auth;
use Philip0514\Ark\Models\Administrator;

class Repository
{
    function __construct()
    {
        $this->administrator = new Administrator;
    }

    public function single($id)
    {
        $rows1 = $this->model->checkTrashed()->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }

    public function findByID($id)
    {
        $rows1 = $this->model->checkTrashed()->find($id);

        if($rows1){
            return $rows1->toArray();
        }
        return null;
    }

    public function findByIDs($id = [])
    {
        if(!sizeof($id) || !$id[0] || !$id){
            return null;
        }
        $rows1 = $this->model->checkTrashed()->whereIn('id', $id)
            ->orderByRaw('FIELD(id, '.implode(', ', $id).')')
            ->get()->toArray();

        return $rows1;
    }

    public function select()
    {
        $rows1 = $this->model;

        return $rows1;
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

        if($deleted){
            $this->delete($id);
        }

        return $id;
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

        $id = $this->model->insertGetId($data);

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

        $this->model
            ->checkTrashed()
            ->where('id', $id)
            ->update($data);

        if($deleted){
            $this->delete($id);
        }
    }

    public function delete($id)
    {
        $this->model
            ->where('id', $id)
            ->delete();
    }

    public function restore($id)
    {
        $this->model
            ->checkTrashed()
            ->find($id)
            ->restore();
    }

    public function datatable($controller)
    {
        $admin = session()->get('admin');

        $parameter = isset($admin['datatable'][$controller]['parameter']) ? $admin['datatable'][$controller]['parameter'] : null;

        $query = $this->model->checkTrashed();
        if($parameter){
            foreach($parameter as $key => $value){
                $continue = 1;
                switch($key){
                    case 'display':
                        $value--;
                        if($value<0){
                            $continue = 0;
                        }
                    break;
                }
                if($continue){
                    $query = $query->where($key, '=', $value);
                }
            }
        }
        return $query;
    }

    protected function path($path)
    {
        $rows1 = explode('/', $path);

        $path = [];
        for($i=0; $i<=1; $i++){
            $path[] = $rows1[$i];
        }

        return '/'.implode('/', $path);
    }

    public function action($type, $rows1)
    {
        $admin_id = Auth::guard('admin')->user()->id;

        $id = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!$rows1[$i]){
                continue;
            }
            $id[] = (int)$rows1[$i];
        }

        switch($type){
            case 1:
                //上架
                $this->actionDisplayTrue($id);
            break;
            case 2:
                //下架
                $this->actionDisplayFalse($id);
            break;
			case 3:
                //永遠刪除
                $this->actionDeleteForce($id);
			break;
			case 4:
				//刪除
                $this->actionDelete($id);
			break;
			case 5:
				//取消刪除
                $this->actionRestore($id);
			break;
			case 6:
				//推薦
                $this->actionRecommendTrue($id);
			break;
			case 7:
				//取消推薦
                $this->actionRecommendFalse($id);
			break;
			case 8:
				//全部匯出
			break;
			case 9:
				//勾選匯出
			break;
			case 10:
			break;
        }
    }

    protected function actionDisplayTrue($id)
    {
        $this->model->whereIn('id', $id)->update([
            'display'       =>  1,
            'updated_by'    =>  Auth::guard('admin')->user()->id,
        ]);
    }

    protected function actionDisplayFalse($id)
    {
        $this->model->whereIn('id', $id)->update([
            'display'       =>  0,
            'updated_by'    =>  Auth::guard('admin')->user()->id,
        ]);
    }

    protected function actionDeleteForce($id)
    {
        $this->model->whereIn('id', $id)->forceDelete();
    }

    protected function actionDelete($id)
    {
        $rows1 = $this->model
        ->whereIn('id', $id);

        $rows1->update([
            'display'       =>  0,
            'deleted_by'    =>  Auth::guard('admin')->user()->id,
        ]);
        $rows1->whereIn('id', $id)->delete();
    }

    protected function actionRestore($id)
    {
        $rows1 = $this->model
        ->whereIn('id', $id);

        $rows1->whereIn('id', $id)->restore();

        $rows1->update([
            'deleted_by'    =>  null,
        ]);
    }

    protected function actionRecommendTrue($id)
    {
        $this->model->whereIn('id', $id)->update([
            'recommend'     =>  1,
            'updated_by'    =>  Auth::guard('admin')->user()->id,
        ]);
    }

    protected function actionRecommendFalse($id)
    {
        $this->model->whereIn('id', $id)->update([
            'recommend'     =>  1,
            'updated_by'    =>  Auth::guard('admin')->user()->id,
        ]);
    }

    public function columnVisible($request)
    {
		$data = $request->input('data', null);
        $admin = session()->get('admin');
        $route = $request->route()->getName();
        list($controller, $name) = explode('.', $route);
        $admin['datatable'][$controller]['columnVisible'] = $data;

        session()->put('admin', $admin);
    }

    public function rowReorder($rows1)
    {
		for($i=0; $i<sizeof($rows1); $i++){
			$this->model->where('id', (int)$rows1[$i][0])->update([
				'sort'	=>	(int)($rows1[$i][1]+1),
			]);
		}
    }

    public function editor($rows1)
    {
        if(isset($rows1['created_by']) && $rows1['created_by']){
            $creator = $this->administrator->find($rows1['created_by']);
            if($creator){
                $rows1['created_by'] = $creator->name;
            }
        }
        if(isset($rows1['updated_by']) && $rows1['updated_by']){
            $updater = $this->administrator->find($rows1['updated_by']);
            if($updater){
                $rows1['updated_by'] = $updater->name;
            }
        }
        if(isset($rows1['deleted_by']) && $rows1['deleted_by']){
            $deleter = $this->administrator->find($rows1['deleted_by']);
            if($deleter){
                $rows1['deleted_by'] = $deleter->name;
            }
        }

        return $rows1;
    }

    public function media($rows1)
    {
		$media_id = [];
		for($i=0; $i<sizeof($rows1); $i++){
            $media_id[] = $rows1[$i]['id'];

            $name = $rows1[$i]['name'];
			list($time, $t) = explode('-', $name);
			$month = date('Ym', $time);

			$rows1[$i]['path'] = sprintf('%s%s/square/%s/%s', config('ark.media.root'), config('ark.media.upload'), $month, $name);
        }

		if(!sizeof($media_id)){
			return [
				'id'	=>	'',
				'data'	=>	[],
			];
		}

		return [
			'id'	=>	implode(',', $media_id),
			'data'	=>	$rows1
		];
    }

    protected function _create($data)
    {
        $admin_id = Auth::guard('admin')->user()->id;
        $data['created_by'] = $data['updated_by'] = $admin_id;

        return $data;
    }

    protected function _update($data)
    {
        $admin_id = Auth::guard('admin')->user()->id;
        $data['updated_by'] = $admin_id;

        return $data;
    }
}