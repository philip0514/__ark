<?php

use Illuminate\Database\Seeder;
use Philip0514\Ark\Traits\Seedable;

class ArkDatabaseSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = __DIR__.'/';
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('StructuresTableSeeder');
        $this->seed('AdministratorsTableSeeder');
        $this->seed('PageTypesTableSeeder');
        $this->seed('ZipsTableSeeder');
    }
}
