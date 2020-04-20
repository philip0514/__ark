<?php

use Illuminate\Database\Seeder;
use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;

class PageTypesTableSeeder extends Seeder
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
                'page_types'
            )
        );

        $id = 1;
        if (!empty($table)) { 
            $id = $table[0]->AUTO_INCREMENT;
        }

        $rows1 = PageType::get();

        $sort = 0;
        $rows2 = [
            [
                'name'          =>  '首頁',
                'slug'          =>  'index',
                'url'           =>  '/',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '會員登入',
                'slug'          =>  'login',
                'url'           =>  '/user/login',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '會員註冊',
                'slug'          =>  'register',
                'url'           =>  '/user/register',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '註冊完成',
                'slug'          =>  'register_complete',
                'url'           =>  '/user/register_complete',
                'sort'          =>  $sort++,
                'editable'      =>  1,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '忘記密碼',
                'slug'          =>  'forgot_password',
                'url'           =>  '/user/forgot_password',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '完成寄送新密碼',
                'slug'          =>  'forgot_password_complete',
                'url'           =>  '/user/forgot_password_complete',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '個人資料',
                'slug'          =>  'user_info',
                'url'           =>  '/user/info',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '個人資料 修改完成',
                'slug'          =>  'info_complete',
                'url'           =>  '/user/info_complete',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '信箱驗證成功',
                'slug'          =>  'verify_success',
                'url'           =>  '/user/verify_success',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '信箱驗證失敗',
                'slug'          =>  'verify_failed',
                'url'           =>  '/user/verify_failed',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '搜尋',
                'slug'          =>  'search',
                'url'           =>  '/search',
                'sort'          =>  $sort++,
                'editable'      =>  0,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '關於我們',
                'slug'          =>  'about',
                'url'           =>  '/about',
                'sort'          =>  $sort++,
                'editable'      =>  1,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '服務條款',
                'slug'          =>  'terms',
                'url'           =>  '/terms',
                'sort'          =>  $sort++,
                'editable'      =>  1,
                'display'       =>  1,
                'text'          =>  null,
            ],
            [
                'name'          =>  '隱私權政策',
                'slug'          =>  'privacy',
                'url'           =>  '/privacy',
                'sort'          =>  $sort++,
                'editable'      =>  1,
                'display'       =>  1,
                'text'          =>  null,
            ],
        ];

        $slug = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!$rows1[$i]->slug){
                continue;
            }
            if(!in_array($rows1[$i]->slug, $slug)){
                $slug[] = $rows1[$i]->slug;
            }
        }

        $data = [];
        for($i=0; $i<sizeof($rows2); $i++){
            if(!in_array($rows2[$i]['slug'], $slug)){
                $data[] = $rows2[$i];
            }
        }

        if($data){
            PageType::insert($data);
        }

    }
}
