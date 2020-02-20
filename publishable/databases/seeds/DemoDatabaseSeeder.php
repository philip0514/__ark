<?php

use Illuminate\Database\Seeder;
use Philip0514\Ark\Traits\Seedable;

class DemoDatabaseSeeder extends Seeder
{
    use Seedable;

    protected $seedersPath = __DIR__.'/demo/';
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->seed('TagsTableSeeder');
        $this->seed('MediaTableSeeder');
        $this->seed('NewsTableSeeder');
        $this->seed('UsersTableSeeder');
    }
}
