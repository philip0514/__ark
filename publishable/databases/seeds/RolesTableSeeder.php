<?php

use Illuminate\Database\Seeder;

use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $structures = DB::table('structures')->select('id', 'url', 'method')->orderBy('id', 'asc')->get()->toArray();
        $structure_id = [];
        for($i=0; $i<sizeof($structures); $i++){
            $structure_id[] = $structures[$i]->id;
        }

        $role = Role::where('name', 'Super Admin')->first();
        if(isset($role->id)){
            return false;
        }

        $role = Role::create([
            'name'          =>  'Super Admin',
            'guard_name'    =>  'admin',
            'structure'     =>  json_encode($structure_id),
            'display'       =>  1,
            'created_at'    =>  date('Y-m-d H:i:s'),
            'updated_at'    =>  date('Y-m-d H:i:s'),
        ]);

        for($i=0; $i<sizeof($structures); $i++){
            if(!$structures[$i]->method){
                continue;
            }
            $method = json_decode($structures[$i]->method, true);

            for($j=0; $j<sizeof($method); $j++){
                $permission_text = sprintf('%s %s', $method[$j], $structures[$i]->url);

                //創造不存在的permission
                Permission::create(['name'  =>  $permission_text, 'guard_name'  =>  'admin']);
                $role->givePermissionTo($permission_text);
            }
        }
    }
}
