<?php
namespace Philip0514\Ark\Serializer\API\V1_0;

use League\Fractal\Resource\Collection;

//Traits
use Philip0514\Ark\Traits\Serializer;
use Philip0514\Ark\Traits\Helper;

class PageSerializer extends Collection
{
	use Serializer, Helper;
	
	public function siteMeta($data)
	{
		$title = isset($data['title']) ? $data['title'] : null;
		$description = isset($data['description']) ? $data['description'] : null;

		$tags = isset($data['tags']) && $data['tags'] ? $data['tags'] : [];
		$tag = $this->tag($tags);

		$ogimages = isset($data['ogimages']) && $data['ogimages'] ? $data['ogimages'] : [];
		$ogimage = $this->ogimage($ogimages);
		
		$data = [
            'title'         =>	$title,
			'site_name'		=>	$title,
            'description'   =>	$description,
			'keywords'       =>	$tag,
			'ogimage'		=>	$ogimage,
		];

		return $data;
	}

	public function meta($data, $site)
	{
		$meta = $site;

		if($data['title']){
			$meta['title'] = $data['title'];
		}

		if($data['description']){
			$meta['description'] = $data['description'];
		}

		$tags = isset($data['tags']) && $data['tags'] ? $data['tags'] : [];
		$tag = $this->tag($tags);
		if($tag){
			$meta['keywords'] = $tag;
		}

		$ogimages = isset($data['ogimages']) && $data['ogimages'] ? $data['ogimages'] : [];
		$ogimage = $this->ogimage($ogimages);
		if($ogimage){
			$meta['ogimage'] = $ogimage;
		}

		return $meta;
	}

	private function tag($tags)
	{
		$tag = [];
		for($i=0; $i<sizeof($tags); $i++){
			$tag[] = $tags[$i]['name'];
		}

		$tag = $tag ? implode(', ', $tag) : null;

		return $tag;
	}

	private function ogimage($ogimages)
	{
		$ogimage = [];
		for($i=0; $i<sizeof($ogimages); $i++){
			$ogimage[] = $this->mediaPath($ogimages[$i]['name'], 'facebook');
		}

		return $ogimage;
	}
}