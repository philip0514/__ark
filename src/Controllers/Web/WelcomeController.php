<?php
namespace Philip0514\Ark\Controllers\Web;

use Philip0514\Ark\Controllers\Web\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\Web\PageBlockRepository;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $block = new PageBlockRepository();
        $data = $block->get('index');
        return $this->view('ark::Web.welcome.index', $data);
    }
}