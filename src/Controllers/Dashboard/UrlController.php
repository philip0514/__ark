<?php
namespace Philip0514\Ark\Controllers\Dashboard;

use Philip0514\Ark\Controllers\Dashboard\Controller;
use Illuminate\Http\Request;
use Philip0514\Ark\Repositories\Dashboard\UrlRepository;

class UrlController extends Controller
{
    protected 	$repo;

	function __construct(
        UrlRepository $UrlRepository
	){
		parent::__construct();
        $this->repo->url = $UrlRepository;
    }
    
    public function manager(Request $request)
    {
        $default = [
            [
                'id'    =>  'none',
                'value' =>  '#',
                'name'  =>  '無',
            ],
            [
                'id'    =>  'index',
                'value' =>  '/',
                'name'  =>  '首頁',
            ],
            [
                'id'    =>  'register',
                'value' =>  '/user/register',
                'name'  =>  '會員註冊',
            ],
            [
                'id'    =>  'login',
                'value' =>  '/user/login',
                'name'  =>  '會員登入',
            ],
            [
                'id'    =>  'forgotPassword',
                'value' =>  '/user/forgot_password',
                'name'  =>  '忘記密碼',
            ],
        ];

        $rows1 = $this->repo->url->news();
        $news = [
            [
                'id'    =>  'news-index',
                'value' =>  '/news',
                'name'  =>  '最新消息 首頁',
            ],
        ];
        for($i=0; $i<sizeof($rows1); $i++){
            $news[] = [
                'id'    =>  sprintf('news-%s', $rows1[$i]['id']),
                'value' =>  sprintf('/news/%s', $rows1[$i]['id']),
                'name'  =>  $rows1[$i]['name'],
            ];
        }

        $data = [
            'default'   =>  $default,
            'news'      =>  $news
        ];
        return view('ark::Dashboard.url.manager', $data);
    }
}