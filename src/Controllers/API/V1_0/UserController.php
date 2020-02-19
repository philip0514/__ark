<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Psr\Http\Message\ServerRequestInterface;

//Exception
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use League\OAuth2\Server\Exception\OAuthServerException;

//Repositories
use Philip0514\Ark\Repositories\API\V1_0\UserRepository;

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

	private $client_id = 2;
	private $repo;

	function __construct(
		UserRepository $userRepository
	)
	{
		$this->repo = new \stdClass();
		$this->repo->user = $userRepository;
	}

	/**
	 *	token
	 *
	 *
	 *
	 *
	 */
	public function token(ServerRequestInterface $request)
	{
		$data = $request->getParsedBody();

		try {
			$grant_type = isset($data['grant_type']) ? $data['grant_type'] : null;
			$client_id = isset($data['client_id']) ? $data['client_id'] : null;
			$client_secret = isset($data['client_secret']) ? $data['client_secret'] : null;

			if(!$grant_type || !$client_id || !$client_secret){
				throw new Exception('token_required');
			}

			switch($grant_type){
				case 'client_credentials':
					$data = $this->repo->user->client($request);
				break;
				case 'password':
					$data = $this->repo->user->password($request);
				break;
				case 'refresh_token':
					$data = $this->repo->user->refresh($request);
				break;
				default:
					throw new Exception('invalid_grant_type');
				break;
			}

			if(isset($data['error'])){
				throw new Exception($data['error']);
			}

			return $this->responseSuccess([
				'data'	=>	$data
			]);
		}
        catch (ModelNotFoundException $e) { 
			// email not found
			return $this->responseError('user_404');
        }
        catch (OAuthServerException $e) {
			//password not correct..token not granted
			return $this->responseError('invalid_credentials');
        }
        catch (Exception $e) {
			//return error message
			$message = $e->getMessage();
			return $this->responseError($message);
        }
	}

	/**
	 *	註冊
	 *
	 *
	 *
	 *
	 */
	public function register(Request $request)
	{
		try{
			$data = $this->repo->user->register($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			//邀請人
			if(isset($data['inviter_id']) && $data['inviter_id']){
				$this->repo->point->inviter($data['inviter_id']);
			}

			//後續處理
			$rows1 = array(
				'id'			=>	$data['user']['id'],
				'name'			=>	$data['user']['name'],
				'email'			=>	$data['user']['email'],
				'password'		=>	$data['user']['password_hidden'],
				'inviter_id'	=>	$data['user']['inviter_id'],
			);
			$this->repo->user->after_register($rows1, 'default');

			unset($data['user']['inviter_id']);
			unset($data['user']['password_hidden']);

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

	/**
	 *	Facebook
	 *
	 *
	 *
	 *
	 */
	public function facebook(Request $request)
	{
		try{
			$data = $this->repo->user->facebook($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

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
	 *
	 *
	 *
	 */
	public function google(Request $request)
	{
		try{
			$data = $this->repo->user->google($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

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
	 *
	 *
	 *
	 */
	public function twitter(Request $request)
	{
		try{
			$data = $this->repo->user->twitter($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

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
	 *
	 *
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

			$this->repo->user->authCodeUpdate($user->id);

			//配發六度幣
			$data = array(
				'user_id'			=>	$user->id,
				'method'			=>	2,
				'object_user_id'	=>	0,
				'amount'			=>	100,
			);
			$this->repo->point->insert($data);

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
	 *
	 *
	 *
	 */
	public function forgot_password(Request $request)
	{
		try{
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

			$data = [
				'password'	=>	$password		//之後要隱藏
			];
			//return $this->responseSuccess();
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
	 *	自己用戶資料
	 *
	 *
	 *
	 *
	 */
	public function infoGet(Request $request)
	{
		try{
			$token = $this->repo->user->parse_token($request);
			$token_id = $token['token_id'];
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
	 *
	 *
	 *
	 */
	public function infoPost(Request $request)
	{
		try{
			$data = $this->repo->user->info_post($request);

			if(isset($data["error"])){
				throw new Exception($data["error"]);
			}

			if($data['inviter_id']){
				$this->repo->point->inviter($data['inviter_id']);
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
	 *
	 *
	 *
	 */
	public function logout(Request $request)
    {
		try{
			$data = $this->repo->user->logout($request);

			if(!$data){
				throw new Exception('invalid_credentials');
			}

			return $this->responseSuccess();
		}
		catch(Exception $e){
			$message = $e->getMessage();
			return $this->responseError($message);
		}
    }
}
