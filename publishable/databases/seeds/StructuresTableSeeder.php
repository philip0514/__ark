<?php

use Illuminate\Database\Seeder;

use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Permission;
use Philip0514\Ark\Models\Structure;

class StructuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $table = DB::select(
            sprintf("SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = '%s' AND TABLE_NAME = '%s'",
                env('DB_DATABASE'),
                'structures'
            )
        );

        $id = 1;
        if (!empty($table)) { 
            $id = $table[0]->AUTO_INCREMENT;
        }

        $rows1 = Structure::get();

        $rows2 = [
            [
                'id'            =>  $id,
                'parent_id'     =>  0,
                'name'          =>  '主控台',
                'icon'          =>  'fa-tachometer-alt',
                'url'           =>  'dashboard',
                'display'       =>  1,
                'sort'          =>  0,
                'editable'      =>  0,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read']),
            ],
            [
                'id'            =>  $id+1,
                'parent_id'     =>  0,
                'name'          =>  '系統',
                'icon'          =>  'fa-users-cog',
                'url'           =>  null,
                'display'       =>  1,
                'sort'          =>  1001,
                'editable'      =>  0,
                'namespace'     =>  null,
                'method'        =>  null,
            ],
            [
                'id'            =>  $id+2,
                'parent_id'     =>  $id+1,
                'name'          =>  '角色',
                'icon'          =>  null,
                'url'           =>  'administratorRole',
                'display'       =>  1,
                'sort'          =>  1002,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+3,
                'parent_id'     =>  $id+1,
                'name'          =>  '管理者',
                'icon'          =>  null,
                'url'           =>  'administrator',
                'display'       =>  1,
                'sort'          =>  1003,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+4,
                'parent_id'     =>  $id+1,
                'name'          =>  '系統設定',
                'icon'          =>  null,
                'url'           =>  'setting',
                'display'       =>  1,
                'sort'          =>  1004,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'update']),
            ],
            [
                'id'            =>  $id+5,
                'parent_id'     =>  0,
                'name'          =>  '最新消息',
                'icon'          =>  'fa-newspaper',
                'url'           =>  'news',
                'display'       =>  1,
                'sort'          =>  502,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+6,
                'parent_id'     =>  0,
                'name'          =>  '會員',
                'icon'          =>  'fa-users',
                'url'           =>  null,
                'display'       =>  1,
                'sort'          =>  503,
                'editable'      =>  0,
                'namespace'     =>  null,
                'method'        =>  null,
            ],
            [
                'id'            =>  $id+7,
                'parent_id'     =>  $id+6,
                'name'          =>  '會員',
                'icon'          =>  null,
                'url'           =>  'user',
                'display'       =>  1,
                'sort'          =>  504,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+8,
                'parent_id'     =>  $id+6,
                'name'          =>  '電子報',
                'icon'          =>  null,
                'url'           =>  'newsletter',
                'display'       =>  1,
                'sort'          =>  505,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'update']),
            ],
            [
                'id'            =>  $id+9,
                'parent_id'     =>  0,
                'name'          =>  '郵件',
                'icon'          =>  'fa-envelope',
                'url'           =>  null,
                'display'       =>  1,
                'sort'          =>  506,
                'editable'      =>  0,
                'namespace'     =>  null,
                'method'        =>  null,
            ],
            [
                'id'            =>  $id+10,
                'parent_id'     =>  $id+9,
                'name'          =>  '郵件樣板',
                'icon'          =>  null,
                'url'           =>  'mailTemplate',
                'display'       =>  1,
                'sort'          =>  507,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+11,
                'parent_id'     =>  $id+9,
                'name'          =>  '郵件歷史',
                'icon'          =>  null,
                'url'           =>  'mail',
                'display'       =>  1,
                'sort'          =>  508,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'update']),
            ],
            [
                'id'            =>  $id+12,
                'parent_id'     =>  0,
                'name'          =>  '媒體',
                'icon'          =>  'fa-images',
                'url'           =>  'media',
                'display'       =>  1,
                'sort'          =>  509,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+13,
                'parent_id'     =>  0,
                'name'          =>  '標籤',
                'icon'          =>  'fa-tags',
                'url'           =>  'tag',
                'display'       =>  1,
                'sort'          =>  510,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'id'            =>  $id+14,
                'parent_id'     =>  0,
                'name'          =>  '頁面',
                'icon'          =>  'fa-file',
                'url'           =>  'page',
                'display'       =>  1,
                'sort'          =>  511,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers\Dashboard',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
        ];

        $url = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!$rows1[$i]->url){
                continue;
            }
            if(!in_array($rows1[$i]->url, $url)){
                $url[] = $rows1[$i]->url;
            }
        }

        $data = $structure_id = [];
        for($i=0; $i<sizeof($rows2); $i++){
            if(!in_array($rows2[$i]['url'], $url)){
                $data[] = $rows2[$i];
            }
        }

        $continue = false;
        for($i=0; $i<sizeof($data); $i++){
            if($data[$i]['url']){
                $continue = true;
            }
        }

        if(!$continue){
            return false;
        }

        if($data){
            Structure::insert($data);

            $structures = Structure::select('id', 'url', 'method')->orderBy('id', 'asc')->get();
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
}
