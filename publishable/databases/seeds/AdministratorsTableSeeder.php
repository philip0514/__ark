<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Philip0514\Ark\Models\Administrator;
use Philip0514\Ark\Models\Role;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::select('name')->first();

        $admin = Administrator::where('id', 1)->first();
        if(isset($admin->id)){
            return false;
        }

        $id = Administrator::insertGetId([
            'name'      => 'Admin',
            'account'   => 'admin',
            'password'	=>	Hash::make('admin'),
            'display'   =>  1,
        ]);

        $admin = Administrator::where('id', $id)->first();
        $admin->assignRole($role->name);
    }
}
