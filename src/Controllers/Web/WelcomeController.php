<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\Web\PageRepository;

class WelcomeController extends Controller
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new \stdClass();
        $this->repo->page = new PageRepository();
    }

    public function index()
    {
        $data = $this->repo->page->get('index');

        return view('ark::Web.welcome.index', $data);
    }
}