<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            StructuresTableSeeder::class,
            RolesTableSeeder::class,
            AdministratorsTableSeeder::class,
            ZipsTableSeeder::class,
        ]);
    }
}
