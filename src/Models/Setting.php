<?php

namespace Philip0514\Ark\Models;

use Illuminate\Database\Eloquent\Model;

//Traits
use Philip0514\Ark\Traits\Helper;

class Setting extends Model
{
    use Helper;

    public function ogimages()
    {
        return $this->morphToMany('App\Models\Media', 'media_relations');
    }

    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'tag_relations');
    }
}
