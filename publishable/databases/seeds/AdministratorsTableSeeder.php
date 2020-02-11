<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use Philip0514\Ark\Models\Role;
use Philip0514\Ark\Models\Administrator;

class AdministratorsTableSeeder extends Seeder
{
	function __construct(
        Administrator $administrator,
        Role $role
	){
		$this->administrator = $administrator;
		$this->role = $role;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = $this->role->select('name')->first()->toArray();

        $id = $this->administrator->insertGetId([
            'name'      => 'Admin',
            'account'   => 'admin',
            'password'	=>	Hash::make('admin'),
            'display'   =>  1,
        ]);

        $admin = $this->administrator->where('id', $id)->first();
        $admin->assignRole($role['name']);
    }
}
