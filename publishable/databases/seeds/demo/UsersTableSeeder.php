<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Philip0514\Ark\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows1 = User::get();

        $rows2 = [
            [
                'name'          =>  'John',
                'email'         =>  'john@example.com',
                'password'      =>  Hash::make('pw1111'),
                'gender'        =>  1,
                'birthday'      =>  '1980/01/02',
                'display'       =>  1,
                'checked'       =>  1,
                'newsletter'    =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Jan',
                'email'         =>  'jan@example.com',
                'password'      =>  Hash::make('pw1111'),
                'gender'        =>  2,
                'birthday'      =>  '1980/01/03',
                'display'       =>  1,
                'checked'       =>  1,
                'newsletter'    =>  0,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
        ];

        $data = $email = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!in_array($rows1[$i]->email, $email)){
                $email[] = $rows1[$i]->email;
            }
        }

        for($i=0; $i<sizeof($rows2); $i++){
            if(!in_array($rows2[$i]['email'], $email)){
                $data[] = $rows2[$i];
            }
        }

        if($data){
            User::insert($data);
        }
    }
}
