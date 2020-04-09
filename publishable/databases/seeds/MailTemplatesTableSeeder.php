<?php

use Illuminate\Database\Seeder;
use Philip0514\Ark\Models\MailType;
use Philip0514\Ark\Models\MailTemplate;

class MailTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = new MailType();
        $count = $type->count();
        if($count){
            return false;
        }
        $data = [
            [
                'id'        =>  1,
                'name'      =>  '郵件外框架',
                'display'   =>  0,
            ],
            [
                'id'        =>  2,
                'name'      =>  '一般註冊',
                'display'   =>  1,
            ],
            [
                'id'        =>  3,
                'name'      =>  'Facebook註冊',
                'display'   =>  1,
            ],
            [
                'id'        =>  4,
                'name'      =>  'Google註冊',
                'display'   =>  1,
            ],
            [
                'id'        =>  5,
                'name'      =>  'Twitter註冊',
                'display'   =>  1,
            ],
            [
                'id'        =>  6,
                'name'      =>  '忘記密碼',
                'display'   =>  1,
            ],
        ];
        $type->insert($data);

        $template = new MailTemplate();
        $count = $template->count();
        if($count){
            return false;
        }
        $data = [
            [
                'id'        =>  1,
                'name'      =>  '註冊成功',
                'display'   =>  0,
            ],
            [
                'id'        =>  2,
                'name'      =>  '註冊成功 - Facebook',
                'display'   =>  1,
            ],
            [
                'id'        =>  3,
                'name'      =>  '註冊成功 - Google',
                'display'   =>  1,
            ],
            [
                'id'        =>  4,
                'name'      =>  '註冊成功 - Twitter',
                'display'   =>  1,
            ],
            [
                'id'        =>  5,
                'name'      =>  '忘記密碼',
                'display'   =>  1,
            ],
        ];
        $template->insert($data);
    }
}
