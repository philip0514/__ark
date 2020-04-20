<?php

namespace Philip0514\Ark\Models;

use Illuminate\Database\Eloquent\Model;

class PageType extends Model
{
    public function pages()
    {
        return $this->hasMany('Philip0514\Ark\Models\Page', 'type');
    }
}
