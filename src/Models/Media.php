<?php

namespace Philip0514\Ark\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//Traits
use Philip0514\Ark\Traits\Helper;

class Media extends Model
{
	use SoftDeletes, Helper;

    public function detachAll($rows1)
    {
        $rows1->abouts()->detach();
        $rows1->news()->detach();
        $rows1->tags()->detach();
        $rows1->setting()->detach();
        $rows1->pages()->detach();

        DB::table('media_relations')->where('media_id', $rows1->id)->delete();
    }

    public function abouts()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\About', 'media_relations');
    }

    public function news()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\News', 'media_relations');
    }

    public function tags()
    {
        return $this->morphToMany('Philip0514\Ark\Models\Tag', 'tag_relations');
    }

    public function setting()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\Setting', 'media_relations');
    }

    public function pages()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\Page', 'media_relations');
    }
}
