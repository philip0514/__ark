<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

//Exception
use Exception;

//Repositories
use Philip0514\Ark\Repositories\API\V1_0\UserRepository;
use Philip0514\Ark\Repositories\API\V1_0\PageRepository;

//Serializer
use Philip0514\Ark\Serializer\API\V1_0\UserSerializer;

//Traits
use Philip0514\Ark\Traits\Response;

/**
 * Class UserController
 * 
 */
class UserController extends Controller
{
	use Response;

	private $repo;

	function __construct(
		UserRepository $UserRepository,
		PageRepository $PageRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->user = $UserRepository;
		$this->repo->page = $PageRepository;
	}

	/**
	 *	註冊
	 *
	 */
	public function register(Request $request)
	{
		try{
            $client_id = $request->input('client_id');
            $email = $request->input('username');
            $password = $request->input('password');
			$name = $request->input('name');

			$data = $this->repo->user->register($email, $password, $name, $client_id);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			$serializer = new UserSerializer();
			$data = $serializer->passwordToken($data);

			return $this->responseSuccess([
				'data'	=>	$data
			],
			201);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	public function registerValidate(Request $request)
	{
		try{
            $username = $request->input('username');

			$data = $this->repo->user->registerValidate($username);

			return $this->responseSuccess([
				'data'	=>	[
					'exist'	=>	$data,
				]
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	Facebook
	 *
	 */
	public function facebook(Request $request)
	{
		try{
            $client_id = $request->input('client_id');
            $email = $request->input('email');
            $facebook_id = $request->input('facebook_id');
			$name = $request->input('name');

			$data = $this->repo->user->facebook($email, $name, $facebook_id, $client_id);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			$serializer = new UserSerializer();
			$data = $serializer->passwordToken($data);

			return $this->responseSuccess([
				'data'	=>	$data
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	Google
	 *
	 */
	public function google(Request $request)
	{
		try{
            $client_id = $request->input('client_id');
            $email = $request->input('email');
            $google_id = $request->input('google_id');
			$name = $request->input('name');

			$data = $this->repo->user->google($email, $name, $google_id, $client_id);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			$serializer = new UserSerializer();
			$data = $serializer->passwordToken($data);

			return $this->responseSuccess([
				'data'	=>	$data
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	Twitter
	 *
	 */
	public function twitter(Request $request)
	{
		try{
            $client_id = $request->input('client_id');
            $email = $request->input('email');
            $twitter_id = $request->input('twitter_id');
			$name = $request->input('name');

			$data = $this->repo->user->twitter($email, $name, $twitter_id, $client_id);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			$serializer = new UserSerializer();
			$data = $serializer->passwordToken($data);

			return $this->responseSuccess([
				'data'	=>	$data
			]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	信件驗證
	 *
	 */
	public function verification(Request $request)
	{
		try{
			$code = $request->input('code');

			if(!$code){
				throw new Exception('verification_required');
			}

			$user = $this->repo->user->findByAuthCode($code);

			if(!$user){
				throw new Exception('user_404');
			}

			if($user->auth_checked===1){
				throw new Exception('verification_checked');
			}

			$this->repo->user->authSuccess($user->id);

			return $this->responseSuccess([]);
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	忘記密碼
	 *
	 */
	public function forgot_password(Request $request)
	{
		try{
			$env = env('APP_ENV');
			$email = $request->input('email');

			if(!$email){
				throw new Exception('forgot_password_required');
			}

			$user = $this->repo->user->findByEmail($email);

			if(!$user){
				throw new Exception('user_404');
			}

			$password = mb_substr(md5($email.uniqid().time()), 0, 10);

			$this->repo->user->passwordUpdate($user->id, $password);

			$this->repo->user->forgotPassword($user, $password);

			if($env=='production'){
				return $this->responseSuccess();
			}else{
				$data = [
					'password'	=>	$password
				];
				return $this->responseSuccess([
					'data'	=>	$data
				]);
			}
		}
		catch(Exception $e){
            $message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	自己用戶資料
	 *
	 */
	public function infoGet(Request $request)
	{
		try{
			$token = $this->repo->user->parse_token($request);
			$user_id = $token['user_id'];

			if(!$user_id){
				throw new Exception('user_404');
			}

			$user = $this->repo->user->findByID($user_id);

			if(!$user){
				throw new Exception('user_404');
			}

			$serializer = new UserSerializer();
			$user = $serializer->info($user);

			$meta = $this->repo->page->meta('user_info');

			return $this->responseSuccess([
				'data'	=>	$user,
				'meta'	=>	$meta
			]);
		}
		catch(Exception $e){
			$message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	更新用戶資料
	 *
	 */
	public function infoPost(Request $request)
	{
		try{
			$data = $this->repo->user->infoPost($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			return $this->responseSuccess();
		}
		catch(Exception $e){
			$message = $e->getMessage();
			return $this->responseError($message);
		}
	}

	/**
	 *	登出
	 *
	 */
	public function logout(Request $request)
    {
		try{
			$this->repo->user->logout($request);

			return $this->responseSuccess();
		}
		catch(Exception $e){
			$message = $e->getMessage();
			return $this->responseError($message);
		}
    }
}
