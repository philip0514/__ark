<?php
namespace Philip0514\Ark\Repositories\Dashboard;

use Philip0514\Ark\Repositories\Dashboard\Repository;

use Auth;
use Illuminate\Support\Facades\Hash;

use Philip0514\Ark\Models\Administrator as Model;
use Philip0514\Ark\Models\Structure;
use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Permission;

class AdministratorRepository extends Repository
{
    protected $model;

	function __construct(
        Model $model,
        Role $role,
        Permission $permission
	){
		parent::__construct();
        $this->model = $model;
		$this->role = $role;
		$this->permission = $permission;
    }

    public function login($credentials)
    {
        //判斷帳號是否存在，並且是否為啟用狀態
        $credentials['display'] = 1;

        //試圖登入
        if (Auth::guard('admin')->attempt($credentials)) {
            // 認證通過...
            return true;
        }

        return false;
    }

    public function loginCheck()
    {
        if(Auth::guard('admin')->check()){
            return true;
        }

        return false;
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
    }

    public function single($id)
    {
        $rows1 = $this->model->checkTrashed()->find($id);

        if($rows1){
            $admin = $rows1->toArray();
            $roles = $rows1->getRoleNames()->toArray();
            $admin['roles'] = $roles;
            if(!$roles){
                $admin['roles'] = [];
            }

            return $admin;
        }
        return null;
    }

    public function create($data)
    {
        $data = $this->_create($data);

        unset($data['id']);

        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $role = $data['role'];
        unset($data['role']);

        $data['password'] = Hash::make($data['password']);

        $id = $this->model->insertGetId($data);

        $this->model->syncRoles($role);

        if($deleted){
            $this->delete($id);
        }else{
            $this->restore($id);
        }

        return $id;
    }

    public function update($data)
    {
        $data = $this->_update($data);

        $id = $data['id'];
        unset($data['id']);

        $role = $data['role'];
        unset($data['role']);

        $deleted = isset($data['deleted']) ? $data['deleted'] : null;
        unset($data['deleted']);

        $data['deleted_by'] = null;
        if($deleted){
            $data['display'] = 0;
            $data['deleted_by'] = $data['updated_by'];
        }else{
            $this->restore($id);
        }

        if($data['password']){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $this->model
            ->checkTrashed()
            ->where('id', $id)
            ->update($data);

        $admin = $this->model->checkTrashed()->find($id);
        $admin->syncRoles($role);

        if($deleted){
            $this->delete($id);
        }
    }

    public function profile_update($data)
    {
        $id = $data['id'];
        unset($data['id']);
        if($data['password']){
            $data['password'] = Hash::make($data['password']);
        }else{
            unset($data['password']);
        }

        $this->model
            ->where('id', $id)
            ->update($data);
    }

    public function validate($account, $id=null)
    {
        $result = $this->model->where('account', $account)->first();

        if($result){
            if($result->id==$id){
                return 'true';
            }
            return 'false';
        }
        return 'true';
    }

    public function role()
    {
        $rows1 = $this->role->where('display', 1)->get()->toArray();

        return $rows1;
    }

    public function profile()
    {
        $rows1 = Auth::guard('admin')->user();

        return $rows1;
    }

    public static function structure()
    {
        if(!\Schema::hasTable('structures')){
            return [];
        }
        $rows1 = Structure::where('display', 1)
            ->whereNotNull('url')
            ->orderBy('sort', 'asc')
            ->get()->toArray();

        $setting = [];
        for($i=0; $i<sizeof($rows1); $i++){
            $setting[] = [
                'namespace'     =>  $rows1[$i]['namespace'],
                'controller'    =>  ucfirst($rows1[$i]['url']).'Controller',
                'url'           =>  $rows1[$i]['url'],
            ];
        }
        return $setting;
    }
}