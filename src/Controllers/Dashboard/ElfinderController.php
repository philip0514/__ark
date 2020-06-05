<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Barryvdh\Elfinder\ElfinderController as Controller;

class ElfinderController extends Controller
{
    public function showTinyMCE5()
    {
        return $this->app['view']
            ->make('ark::Dashboard.elfinder.tinymce5')
            ->with($this->getViewVars());
    }
}
?>