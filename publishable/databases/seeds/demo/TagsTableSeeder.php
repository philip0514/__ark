<?php

use Illuminate\Database\Seeder;

use Philip0514\Ark\Models\Tag;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $rows1 = Tag::get();

        $rows2 = [
            [
                'name'          =>  'Ark',
                'slug'          =>  'ark',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Sun',
                'slug'          =>  'sun',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Sea',
                'slug'          =>  'sea',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Land',
                'slug'          =>  'land',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Mountain',
                'slug'          =>  'Mountain',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Moon',
                'slug'          =>  'moon',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  'Star',
                'slug'          =>  'star',
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
        ];

        $data = $slug = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!in_array($rows1[$i]->slug, $slug)){
                $slug[] = $rows1[$i]->slug;
            }
        }

        for($i=0; $i<sizeof($rows2); $i++){
            if(!in_array($rows2[$i]['slug'], $slug)){
                $data[] = $rows2[$i];
            }
        }

        if($data){
            Tag::insert($data);
        }
    }
}
