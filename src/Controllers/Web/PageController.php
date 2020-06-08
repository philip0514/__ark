<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\Web\PageRepository;

class PageController extends Controller
{
    public function __invoke($url)
    {
        $page = new PageRepository();
        $data = $page->get($url);
        if(!$data){
            abort(404);
        }

        return view('ark::Web.welcome.index', $data);
    }
}