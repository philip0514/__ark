<?php
namespace Philip0514\Ark\Controllers;

use Philip0514\Ark\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

//Repositories
use Philip0514\Ark\Repositories\AdministratorRepository as MainRepo;;

class DashboardController extends Controller
{
    protected 	$repo, 
				$config,
				$path,
				$method = 'get',
				$route_index;

	function __construct(
        Request $request,
		MainRepo $main
	)
	{
		parent::__construct();
        $this->repo->main = $main;
        $this->method = strtolower($request->method());
        $this->path = $request->path();

		$route = $request->route()->getName();
		$this->route_index = sprintf('%s.index', $route);

        $this->config  = [
			'name'				=>	'主控台',
			'route'				=>	$route,
            'controller'		=>	'dashboard',
        ];
    }

    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user()->toArray();

        $data = [
            'config'    =>  $this->config,
            'admin'     =>  $admin,
        ];
        return $this->view('ark.dashboard.index', $data);
    }

    public function login(Request $request)
    {
        switch($this->method){
            case 'post':
                $credentials = $request->only('account', 'password');
                $status = $this->repo->main->login($credentials);

                if(!$status){
                    return back()->with('status', 'login_failed');
                }else{
                    $admin = Auth::guard('admin')->user();
                    $rows1 = $admin->getAllPermissions()->toArray();
                    $permission = [];
                    for($i=0; $i<sizeof($rows1); $i++){
                        $permission[] = $rows1[$i]['name'];
                    }
                    session()->put( config('ark.permission') , $permission);

                    $url = session()->get( config('ark.session_url') );
                    if($url){
                        return redirect($url);
                    }
                    return redirect()->route('dashboard');
                }
            break;
        }

        if($this->repo->main->loginCheck()){
			return redirect()->route('dashboard');
        }

        return view('ark.dashboard.login');
    }

    public function logout(Request $request)
    {
        //session()->forget('administrator_info');
        $this->repo->main->logout();

        return redirect()->route('login');
    }

    public function showRoute(Request $request)
    {
        $rows1 = [
            'request.toggle_sidebar',
            'request.zip',
            'tag.seark',
            'tag.insert',
            'media.manager',
            'media.upload',
            'media.data',
            'media.editor',
            'product.createSpec',
            'product.createCategory',
            'product.createColor',
            'product.createStyle',
            'product.createInventory',
            'product.createPlus',
            'product.datatablePrice',
            'product.datatablePlus',
            'product.datatableInventory',
            'product.price',
            'product.inventory',
            'product.plus',
            'product.seark',
            'productCategory.product',
            'coupon.datatableUser',
            'coupon.user',
            'user.seark',
        ];

        $result = [];
        for($i=0; $i<sizeof($rows1); $i++){
            $rows2 = explode('.', $rows1[$i]);

            switch(sizeof($rows2)){
                case 2:
                    $result[ $rows2[0] ][ $rows2[1] ] = $this->route_uri($rows1[$i]);
                break;
            }
        }

        $data = [
            'route'     =>  json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT),
        ];
        $contents =  view('ark.dashboard.route', $data);

        return response($contents, 200)->header('Content-Type', 'application/javascript');
    }

    private function route_uri($name)
    {
        return app('router')->getRoutes()->getByName($name)->uri();
    }
}