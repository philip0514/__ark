<?php

namespace Philip0514\Ark\Models;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//Traits
use Philip0514\Ark\Traits\Helper;

class Tag extends Model
{
    use SoftDeletes, Helper;

    public function detachAll($rows1)
    {
        $rows1->media()->detach();
        $rows1->setting()->detach();

        DB::table('tag_relations')->where('tag_id', $rows1->id)->delete();
    }

    public function media()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\Media', 'tag_relations');
    }

    public function setting()
    {
        return $this->morphedByMany('Philip0514\Ark\Models\Setting', 'tag_relations');
    }
}
