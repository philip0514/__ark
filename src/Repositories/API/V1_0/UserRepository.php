<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Lcobucci\JWT\Parser;
use Carbon\Carbon;

//Exception
use Exception;

//Models
use DB;
use Philip0514\Ark\Models\User;

//Traits
use Philip0514\Ark\Traits\PassportToken;

class UserRepository
{
    use PassportToken;

    private $user;
    private $atc;
	private $client_id = 2;

    function __construct(
        User $user, 
        AccessTokenController $atc
    )
    {
        $this->user = $user;
        $this->atc = $atc;
    }

    /**
     * client token
     *
     */
    public function client($request)
    {
        $tokenResponse = $this->atc->issueToken($request);
        $content = $tokenResponse->getContent();
        $data = json_decode($content, true);

        return $data;
    }

    /**
     * 登入
     *
     */
    public function password($request)
    {
        try{
            $data = $request->getParsedBody();
            $username = $data['username'];
            $password = isset($data['password']) ? $data['password'] : '';

            if(!$username || !$password){
                throw new Exception();
            }

            $user = $this->user
                ->where('email', $username)
                ->where('display', 1)
                ->first();

            if(!isset($user->id) || !password_verify($password, $user->password)){
                throw new Exception();
            }

            $tokenResponse = $this->atc->issueToken($request);
            $content = $tokenResponse->getContent();
            $data = json_decode($content, true);

            $data['user'] = $user->toArray();

            return $data;
        }
        catch (Exception $e) {
            $data = [
                'error'			=>	'invaild_login',
            ];
            return $data;
        }
    }

    /**
     * refresh token
     *
     */
    public function refresh($request)
    {
        try{

            $data = $request->getParsedBody();
            $refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : null;

            if(!$refresh_token){
                throw new Exception('refresh_token_required');
            }

            $tokenResponse = $this->atc->issueToken($request);
            $content = $tokenResponse->getContent();
            $data = json_decode($content, true);

            if(isset($data['error'])){
                throw new Exception('invalid_refresh_token');
            }
    
            return $data;
        }
        catch (Exception $e) {
            $data = [
                'error'			=>	$e->getMessage(),
            ];
            return $data;
        }
    }

	public function parse_token($request)
	{
        $bearerToken = $request->bearerToken();
        $token = (new Parser())->parse($bearerToken);
        $token_id = $token->getClaim('jti');
        $token =  DB::table('oauth_access_tokens')->where('id', '=', $token_id)->first();
        $user_id = $token->user_id;

		return [
			'token_id'	=>	$token_id,
			'user_id'	=>	$user_id,
		];
	}

    /**
     * 註冊
     *
     */
    public function register($email, $password, $name, $client_id=null)
    {
        try{
            $client_id = $client_id ? $client_id : $this->client_id;

            if(!$email || !$password || !$name){
                throw new Exception('register_required');
            }

            $user = $this->user->where('email', '=', $email)->select('id')->first();
            
            if(isset($user->id) && $user->id){
                throw new Exception('user_existed');
            }

            //註冊
            $rows1 = [
                'email'			=>	$email,
                'password'		=>	Hash::make($password),
                'name'			=>	$name,
            ];
            $user_id = $this->user->insertGetId($rows1);

            $user = $this->user->find($user_id);
            $this->after_register($user);

            $rows1 = $this->getBearerTokenByUser($user, $client_id, false);
            $rows1['user'] = $user;

            return $rows1;
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    /**
     * 註冊之後
     *
     */
	public function after_register($data=[], $email_type='default')
	{
        $user_id = $data['id'];
		$name = $data['name'];
		$email = $data['email'];

		//驗證碼
		$checked_auth = md5(uniqid().time());
		$auth_url = config('api.frontend.user.verify').$checked_auth;

        $code = $this->num2alpha($user_id);

		$this->user->where('id', '=', $user_id)->update([
			'code'			=>	$code,
			'checked_auth'	=>	$checked_auth,
			'checked'	    =>	0,
        ]);

        /*
		//mail
		switch($email_type){
			case 'facebook':
				$data = [
					'name'		=> $name,
					'email'		=> $email,
					'auth_url'	=> $auth_url,
				];
				$content = view('mail.register_facebook', $data);

				$data = [
					'name'			=>	'【明鏡】会员注册通知信 - Facebook',
					'content'		=>	$content,
					'user_id'		=>	$user_id,
					'user_name'		=>	$name,
					'user_email'	=>	$email,
					'created_at'	=>	Carbon::now(),
					'updated_at'	=>	Carbon::now(),
				];
				break;
			case 'google':
				$data = [
					'name'		=> $name,
					'email'		=> $email,
					'auth_url'	=> $auth_url,
				];
				$content = view('mail.register_google', $data);

				$data = [
					'name'			=>	'【明鏡】会员注册通知信 - Google',
					'content'		=>	$content,
					'user_id'		=>	$user_id,
					'user_name'		=>	$name,
					'user_email'	=>	$email,
					'created_at'	=>	Carbon::now(),
					'updated_at'	=>	Carbon::now(),
				];
				break;
			case 'twitter':
				$data = [
					'name'		=> $name,
					'email'		=> $email,
					'auth_url'	=> $auth_url,
				];
				$content = view('mail.register_twitter', $data);

				$data = [
					'name'			=>	'【明鏡】会员注册通知信 - Twitter',
					'content'		=>	$content,
					'user_id'		=>	$user_id,
					'user_name'		=>	$name,
					'user_email'	=>	$email,
					'created_at'	=>	Carbon::now(),
					'updated_at'	=>	Carbon::now(),
				];
				break;
			default:
			case 'default':
				$data = [
					'name'		=> $name,
					'email'		=> $email,
					'auth_url'	=> $auth_url,
				];
				$content = view('mail.register', $data);

				$data = [
					'name'			=>	'【明鏡】会员注册通知信',
					'content'		=>	$content,
					'user_id'		=>	$user_id,
					'user_name'		=>	$name,
					'user_email'	=>	$email,
					'created_at'	=>	Carbon::now(),
					'updated_at'	=>	Carbon::now(),
				];
				break;
		}

        $this->mail->insert($data);
        */
    }

    public function num2alpha($id)
    {
		//數字轉英文(0=>A、1=>B、26=>AA...以此類推)

		$id_number = substr($id, -3);
		$id_text = (int)substr($id, 0, -3);

		for($r = ""; $id_text >= 0; $id_text = intval($id_text / 26) - 1){
			$r = chr($id_text%26 + 0x41) . $r; 
		}
		if(strlen($id_number)<3){
			$id_number = str_pad($id_number, 3, 0, STR_PAD_LEFT);
		}
		if(strlen($r)==1){
			$r = 'A'.$r;
		}

		return $r.$id_number;
	}

    /**
     * Facebook register/login
     *
     */
    public function facebook($email, $name, $facebook_id, $client_id=null)
    {
        try{
            $client_id = $client_id ? $client_id : $this->client_id;
            $password = implode(',', array($email, $facebook_id, time(), uniqid()));

            if(!$facebook_id){
                throw new Exception('facebook_required');
            }

            $user = $this->user
                    ->where('facebook_id', '=', $facebook_id)
                    ->first();

            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('facebook_required');
                }

                $user = $this->user
                        ->where('email', '=', $email)
                        ->first();

                if($user){
                    //若email存在，則寫入 facebook_id
                    $this->user
                    ->where('id', $user->id)
                    ->update([
                        'facebook_id'	=>	$facebook_id
                    ]);
                    $user->facebook_id = $facebook_id;
                }else{
                    $rows1 = [
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'facebook_id'	=>	$facebook_id,
                    ];
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $user = $this->user->find($user_id);
                    $this->after_register($user, 'facebook');
                }
            }else{
                if(!$user->display){
                    throw new Exception('user_disabled');
                }
            }

            //登入
            $rows1 = $this->getBearerTokenByUser($user, $client_id, false);
            $rows1['user'] = $user;

            return $rows1;
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    /**
     * google register/login
     *
     */
    public function google($email, $name, $google_id, $client_id=null)
    {
        try{
            $client_id = $client_id ? $client_id : $this->client_id;
            $password = implode(',', array($email, $google_id, time(), uniqid()));

            if(!$google_id){
                throw new Exception('google_required');
            }

            $user = $this->user
                    ->where('google_id', '=', $google_id)
                    ->first();

            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('google_required');
                }

                $user = $this->user
                        ->where('email', '=', $email)
                        ->first();

                if($user){
                    //若email存在，則寫入 google_id
                    $this->user
                        ->where('id', $user->id)
                        ->update([
                            'google_id'	=>	$google_id
                        ]);
                    $user->google_id = $google_id;
                }else{

                    $rows1 = [
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'google_id'	    =>	$google_id,
                    ];
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $user = $this->user->find($user_id);
                    $this->after_register($user, 'google');
                }
            }else{
                if(!$user->display){
                    throw new Exception('user_disabled');
                }
            }

            //登入
            $rows1 = $this->getBearerTokenByUser($user, $client_id, false);
            $rows1['user'] = $user;

            return $rows1;
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    /**
     * twitter register/login
     *
     */
    public function twitter($email, $name, $twitter_id, $client_id=null)
    {
        try{
            $client_id = $client_id ? $client_id : $this->client_id;
            $password = implode(',', array($email, $twitter_id, time(), uniqid()));

            if(!$twitter_id){
                throw new Exception('twitter_required');
            }

            $user = $this->user
                    ->where('twitter_id', '=', $twitter_id)
                    ->first();

            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('twitter_required');
                }

                $user = $this->user
                        ->where('email', '=', $email)
                        ->first();

                if($user){
                    //若email存在，則寫入 twitter_id
                    $this->user
                    ->where('id', $user->id)
                    ->update([
                        'twitter_id'	=>	$twitter_id
                    ]);
                    $user->twitter_id = $twitter_id;
                }else{
                    $rows1 = [
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'twitter_id'	=>	$twitter_id,
                    ];
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $user = $this->user->find($user_id);
                    $this->after_register($user, 'twitter');
                }
            }else{
                if(!$user->display){
                    throw new Exception('user_disabled');
                }
            }

            //登入
            $rows1 = $this->getBearerTokenByUser($user, $client_id, false);
            $rows1['user'] = $user;

            return $rows1;
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    public function authCodeGet($code)
    {
		$user = $this->user
            ->where('checked_auth', '=', $code)
            ->first();

        return $user;
    }

    public function authSuccess($user_id)
    {
        $this->user
            ->where('id', '=', $user_id)
            ->update([
                'checked'	=>	1
            ]);
    }

    public function findByEmail($email)
    {
        $user = $this->user->where('email', '=', $email)->first();

        return $user;
    }

    public function findByID($id)
    {
        $user = $this->user->find($id);

        return $user;
    }

    public function findByIDs($id)
    {
        $user = $this->user
            ->whereIn('id', $id)
            ->orderByRaw(sprintf('FIELD(id, %s)', implode(', ', $id)))
            ->get()
            ->toArray();

        return $user;
    }

    public function findByAuthCode($code)
    {
		$user = $this->user->where('checked_auth', '=', $code)->first();

        return $user;
    }

    public function findByCode($code)
    {
		$user = $this->user->where('code', '=', $code)->first();

        return $user;
    }

    public function passwordUpdate($user_id, $password)
    {
        $this->user
            ->where('id', $user_id)
		    ->update([
                'password' => Hash::make($password)
            ]);
    }
    public function forgotPassword($user, $password)
    {
        /*
		$data = [
			'name'		=>	$user->name,
			'email'		=>	$user->email,
			'password'	=>	$password,
			'url'		=>	config('api.frontend.user.login'),
		];
		$content = view('mail.forgot_password', $data);

		$data = [
			'name'			=>	'【明鏡】会员忘记密码通知信',
			'content'		=>	$content,
			'user_id'		=>	$user->id,
			'user_name'		=>	$user->name,
			'user_email'	=>	$user->email,
			'created_at'	=>	Carbon::now(),
			'updated_at'	=>	Carbon::now(),
		];
        $this->mail->insert($data);
        */
    }

    public function infoPost($request)
    {
        try{
            $token = $this->parse_token($request);
            $user_id = $token['user_id'];

			if(!$user_id){
				throw new Exception('user_404');
			}

			$name = strip_tags($request->input('name'));
			$email = $request->input('email');
			$password = $request->input('password');
			$gender = (int)$request->input('gender');
			$description = strip_tags($request->input('description'));
			$birthday = strtotime($request->input('birthday')) ? date('Y-m-d', strtotime($request->input('birthday'))) : null;

			if(!$name){
				throw new Exception('info_required');
			}

			$data = array(
				'name'			=>	$name,
				'email'			=>	$email,
				'gender'		=>	$gender,
				'birthday'		=>	$birthday,
				'description'	=>	$description,
			);

            $inviter_id = null;

			if($password){
				$data['password'] = Hash::make($password);
			}

			if(!$email){
				unset($data['email']);
			}

            $this->user->where('id', '=', $user_id)->update($data);
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    public function logout($request)
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getClaim('jti');
        $token = $request->user()->tokens->find($id);
        $token->revoke();
    }
}