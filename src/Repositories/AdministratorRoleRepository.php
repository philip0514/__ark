<?php
namespace Philip0514\Ark\Repositories;

use Philip0514\Ark\Repositories\Repository;

use DB;
use Philip0514\Ark\Models\Structure;
use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Permission;

class AdministratorRoleRepository extends Repository
{
    protected $model;

	function __construct(
        Structure $structure,
        Role $role,
        Permission $permission
	){
		$this->model = $role;
		$this->structure = $structure;
		$this->role = $role;
        $this->permission = $permission;
    }

    public function create($data)
    {
        $permission = $data['permission'];
        unset($data['id']);
        unset($data['permission']);
        $result = $this->role->create($data);

        $this->rolePermission($result->id, $permission);

        return $result->id;
    }

    public function update($data)
    {
        $id = $data['id'];
        $permission = $data['permission'];
        unset($data['id']);
        unset($data['permission']);

        $this->role
            ->where('id', $id)
            ->update($data);

        $this->rolePermission($id, $permission);
    }

    public function validate($name, $id=null)
    {
        $result = $this->model->where('name', $name)->first();

        if($result){
            if($result->id==$id){
                return 'true';
            }
            return 'false';
        }
        return 'true';
    }

    public function structure()
    {
        $rows1 = $this->structure->where('display', 1)->orderBy('sort', 'asc')->get()->toArray();

        return $rows1;
    }

    public function getRolePermissions($id)
    {
        $rows1 = DB::select(
            'SELECT B.* 
            FROM role_has_permissions as A 
            LEFT JOIN permissions as B ON A.permission_id=B.id
            WHERE A.role_id=?', 
            [$id]
        );

        $permission = [];
        for($i=0; $i<sizeof($rows1); $i++){
            list($method, $url) = explode(' ', $rows1[$i]->name);
            $permission[ $url ][] = $method;
        }
        return $permission;
    }

    private function rolePermission($id, $permission)
    {
        $role = $this->role->findById($id);
        $rows1 = $this->structure();

        for($i=0; $i<sizeof($rows1); $i++){
            $method = json_decode($rows1[$i]['method'], true);

            if(!$method){
                continue;
            }

            for($j=0; $j<sizeof($method); $j++){
                $permission_text = sprintf('%s %s', $method[$j], $rows1[$i]['url']);

                //創造不存在的permission
                $this->permission->findOrCreate($permission_text)->toArray();

                //revoke permission
                $role->revokePermissionTo($permission_text);
            }

            //給予權限
            if(isset($permission[ $rows1[$i]['id'] ])){
                $permission_method = $permission[ $rows1[$i]['id'] ];
                for($j=0; $j<sizeof($permission_method); $j++){
                    $permission_text = sprintf('%s %s', $permission_method[$j], $rows1[$i]['url']);
                    $role->givePermissionTo($permission_text);
                }
            }
        }
    }
}