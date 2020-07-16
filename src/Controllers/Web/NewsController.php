<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\Web\PageRepository;
use Philip0514\Ark\Repositories\Web\NewsRepository;

class NewsController extends Controller
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new \stdClass();
        $this->repo->page = new PageRepository();
        $this->repo->news = new NewsRepository();
    }

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $data = $this->repo->news->index($page, $limit);

        return view('ark::Web.news.index', $data);
    }

    public function show(Request $request, $id)
    {
        $data = $this->repo->news->show($id);

        return view('ark::Web.news.show', $data);
    }
}