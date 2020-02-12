<?php
namespace Philip0514\Ark\Controllers;

use Barryvdh\Elfinder\ElfinderController as Controller;

class ElfinderController extends Controller
{
    public function showTinyMCE5()
    {
        return $this->app['view']
            ->make($this->package . '::tinymce5')
            ->with($this->getViewVars());
    }
}
?>