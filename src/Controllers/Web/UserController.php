<?php
namespace Philip0514\Ark\Controllers\Web;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

use Philip0514\Ark\Repositories\API\V1_0\UserRepository;
use Philip0514\Ark\Repositories\Web\PageRepository;

class UserController extends Controller
{
    protected 	$repo;

    private function init()
    {
        $this->repo = new \stdClass();
        $this->repo->user = new UserRepository();
        $this->repo->page = new PageRepository();
    }

    public function login(Request $request)
    {
        $this->init();
        $method = strtolower($request->method());

        switch($method){
            case 'post':
                $username = $request->input('username', null);
                $password = $request->input('password', null);

                $data = $this->repo->user->password($username, $password);
                if(!$data){
                    return back()->with('status', 'login_failed');
                }else{
                    /*
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
                    */
                }
                dd([
                    $username,
                    $password
                ]);
            break;
        }
        $data = $this->repo->page->get('login');

        return view('ark::Web.user.login', $data);
    }
}