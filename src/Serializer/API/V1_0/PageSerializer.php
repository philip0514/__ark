<?php
namespace Philip0514\Ark\Serializer\API\V1_0;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;
use Philip0514\Ark\Traits\Helper;

class PageSerializer extends Collection
{
	use Serializer, Helper;
	
	public function meta($data, $site=null)
	{
		$tags = isset($data['tags']) && $data['tags'] ? $data['tags'] : (isset($site['tags']) ? $site['tags'] : []);
		$tag = [];
		for($i=0; $i<sizeof($tags); $i++){
			$tag[] = $tags[$i]['name'];
		}

		$ogimages = isset($data['ogimages']) && $data['ogimages'] ? $data['ogimages'] : (isset($site['ogimages']) ? $site['ogimages'] : []);
		$image = [];
		for($i=0; $i<sizeof($ogimages); $i++){
			$image[] = $this->mediaPath($ogimages[$i]['name'], 'facebook');
		}

		$title = isset($data['title']) ? $data['title'] : (isset($site['title']) ? $site['title'] : null);
		$description = isset($data['description']) ? $data['description'] : (isset($site['description']) ? $site['description'] : null);

        $meta = [
            'title'         => $title,
            'description'   => $description,
            'keyword'       => $tag ? implode(', ', $tag) : null,
            'og'            => [
                'title'         => $title,
                'description'   => $description,
                'site_name'     => $site['title'],
                'image'         => $image ? $image : null,
            ],
		];
		
		return $meta;
	}
}