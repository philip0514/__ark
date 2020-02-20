<?php

use Illuminate\Database\Seeder;

use Philip0514\Ark\Models\News;

class NewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows1 = News::get();

        $rows2 = [
            [
                'name'          =>  '測試新聞1',
                'description'   =>  '測試新聞1',
                'content'       =>  '<p>測試 新聞 1</p>',
                'news_time'     =>  time(),
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
            [
                'name'          =>  '測試新聞2',
                'description'   =>  '測試新聞2',
                'content'       =>  '<p>測試 新聞 2</p>',
                'news_time'     =>  time(),
                'display'       =>  1,
                'created_by'    =>  1,
                'updated_by'    =>  1,
            ],
        ];

        $data = $name = [];
        for($i=0; $i<sizeof($rows1); $i++){
            if(!in_array($rows1[$i]->name, $name)){
                $name[] = $rows1[$i]->name;
            }
        }

        for($i=0; $i<sizeof($rows2); $i++){
            if(!in_array($rows2[$i]['name'], $name)){
                $data[] = $rows2[$i];
            }
        }

        if($data){
            for($i=0; $i<sizeof($data); $i++){
                $id = News::insertGetId($data[$i]);
                $rows1 = News::find($id);

                $ogimage_id = rand(1, 5);
                $ogimage[ $ogimage_id ] = [
                    'sort'		=>	0,
                    'type'		=>	'ogimage',
                ];
                $rows1->media()->sync($ogimage);
            }
        }
    }
}
