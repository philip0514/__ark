<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\Web\PageRepository;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $page = new PageRepository();
        $data = $page->get('index');

        return view('ark::Web.welcome.index', $data);
    }
}