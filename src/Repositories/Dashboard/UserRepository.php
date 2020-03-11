<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Illuminate\Support\Facades\Hash;

use Philip0514\Ark\Models\User as Model;

class UserRepository extends Repository
{
    protected $model;

	function __construct(
		Model $model
	){
		parent::__construct();
		$this->model = $model;
	}

	public function newsletter_datatable($controller)
	{
        $admin = session()->get('admin');

        $parameter = isset($admin['datatable'][$controller]['parameter']) ? $admin['datatable'][$controller]['parameter'] : null;

		$query = $this->model->query();
		$query = $query->where('newsletter', 1);
        if($parameter){
            foreach($parameter as $key => $value){
                switch($key){
                    case 'display':
                        $value--;
                        if($value<0){
                            continue;
                        }
                    break;
                }
                $query = $query->where($key, '=', $value);
            }
        }
        return $query;
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

        if($data['password']){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
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
        if(isset($data['password'])){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $data['deleted_by'] = null;
        if($deleted){
            $data['display'] = 0;
            $data['deleted_by'] = $data['updated_by'];
        }else{
            $this->restore($id);
        }

        $this->model
            ->where('id', $id)
            ->update($data);

        if($deleted){
            $this->delete($id);
        }
    }

    public function validate($email, $id=null)
    {
        $result = $this->model->checkTrashed()->where('email', $email)->first();

        if($result){
            if($result->id==$id){
                return 'true';
            }
            return 'false';
        }
        return 'true';
    }

	public function search($text)
	{
        $rows1 = $this->model
            ->checkTrashed()
			->select('id', 'name as text')
			->where(function ($query) use ($text) {
                $query->where('name', 'like', '%'.$text.'%')->orWhere('id', '=', $text);
            })
			->get()->toArray();
		return $rows1;
	}
}