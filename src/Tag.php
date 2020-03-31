<?php
namespace Philip0514\Ark;

class Tag {

    public function slug($name)
	{
		$name = str_replace(' ', '', preg_replace(config('ark.url_allow_chars'), '' , strtolower(trim($name))));
	
		return $name;
	}
}