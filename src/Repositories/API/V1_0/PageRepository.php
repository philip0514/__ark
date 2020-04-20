<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Philip0514\Ark\Models\PageType;
use Philip0514\Ark\Models\Page;
use Philip0514\Ark\Models\Setting;

use Philip0514\Ark\Serializer\API\V1_0\PageSerializer;

class PageRepository
{
    public $spliter, $type, $page, $setting;

	function __construct()
	{
        $this->spliter = config('ark.spliter');
        $this->type = new PageType();
        $this->page = new Page();
        $this->setting = new Setting();
    }

    public function meta($slug=null)
    {
        $serializer = new PageSerializer();

        $site = $this->setting
        ->with([
            'ogimages'	=>	function($query){
                $query->orderBy('sort', 'asc');
            },
            'tags'
        ])
        ->orderBy('id', 'desc')
        ->first();

        if($site){
            $site = $site->toArray();
        }

        if(!$slug){
            return $serializer->meta($site);
        }

        $type = $this->type
        ->with([
            'pages' =>  function($query){
                $query
                ->with([
                    'ogimages',
                    'tags'
                ])
                ->orderBy('id', 'desc')->first();
            }
        ])
        ->where('slug', $slug)->first();

        if(!isset($type->pages[0]['id'])){
            return $serializer->meta($site);
        }

        $page = $type->pages[0]->toArray();

        return $serializer->meta($page, $site);
    }
}
