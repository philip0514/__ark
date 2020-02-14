<?php

use Illuminate\Database\Seeder;

class StructuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = DB::table('structures')->count();
        if($count){
            return false;
        }

        DB::table('structures')->insert([
            [
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
                'parent_id'     =>  1,
                'name'          =>  '角色',
                'icon'          =>  null,
                'url'           =>  'administratorRole',
                'display'       =>  1,
                'sort'          =>  1002,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'parent_id'     =>  1,
                'name'          =>  '管理者',
                'icon'          =>  null,
                'url'           =>  'administrator',
                'display'       =>  1,
                'sort'          =>  1003,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'parent_id'     =>  1,
                'name'          =>  '系統設定',
                'icon'          =>  null,
                'url'           =>  'setting',
                'display'       =>  1,
                'sort'          =>  1004,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'update']),
            ],
            [
                'parent_id'     =>  0,
                'name'          =>  '關於我們',
                'icon'          =>  'fa-keyboard',
                'url'           =>  'about',
                'display'       =>  1,
                'sort'          =>  501,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [   //6
                'parent_id'     =>  0,
                'name'          =>  '最新消息',
                'icon'          =>  'fa-newspaper',
                'url'           =>  'news',
                'display'       =>  1,
                'sort'          =>  502,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [   //7
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
                'parent_id'     =>  7,
                'name'          =>  '會員',
                'icon'          =>  null,
                'url'           =>  'user',
                'display'       =>  1,
                'sort'          =>  504,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'parent_id'     =>  7,
                'name'          =>  '電子報',
                'icon'          =>  null,
                'url'           =>  'newsletter',
                'display'       =>  1,
                'sort'          =>  505,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'update']),
            ],
            [   //10
                'parent_id'     =>  0,
                'name'          =>  '媒體',
                'icon'          =>  'fa-images',
                'url'           =>  'media',
                'display'       =>  1,
                'sort'          =>  506,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
            [
                'parent_id'     =>  0,
                'name'          =>  '標籤',
                'icon'          =>  'fa-tags',
                'url'           =>  'tag',
                'display'       =>  1,
                'sort'          =>  507,
                'editable'      =>  1,
                'namespace'     =>  '\Philip0514\Ark\Controllers',
                'method'        =>  json_encode(['read', 'create', 'update', 'delete']),
            ],
        ]);
    }
}
