<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

class PageRepository
{
    public $spliter = ' ::: ';

    public function meta($type, $data = null)
    {
        $meta = array(
            'title'         => null,
            'description'   => null,
            'keyword'       => null,
            'og'            => [
                'image'         => null,
                'site_name'     => null,
                'title'         => null,
                'description'   => null,
            ],
        );

        return $meta;
    }
}
