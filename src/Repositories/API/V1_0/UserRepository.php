<?php
namespace Philip0514\Ark\Repositories\API\V1_0;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Lcobucci\JWT\Parser;
use Philip0514\Ark\Mail\Mailer;

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
	private $client_id = 2;

    function __construct()
    {
        $this->user = new User();
    }

    /**
     * 登入
     *
     */
    public function password($username, $password)
    {
        if(!$username || !$password){
            return null;
        }

        $user = $this->user
        ->where('email', $username)
        ->where('display', 1)
        ->first();

        if(!isset($user->id) || !password_verify($password, $user->password)){
            return null;
        }

        return $user->toArray();
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
            $this->afterRegister($user, 2);

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
     * Facebook register/login
     *
     */
    public function facebook($email, $name, $facebook_id, $client_id=null)
    {
        try{
            $client_id = $client_id ? $client_id : $this->client_id;
            $password = implode(',', [$email, $facebook_id, time(), uniqid()]);

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
                    $this->afterRegister($user, 3);
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
            $password = implode(',', [$email, $google_id, time(), uniqid()]);

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
                    $this->afterRegister($user, 4);
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
            $password = implode(',', [$email, $twitter_id, time(), uniqid()]);

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
                    $this->afterRegister($user, 5);
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
     * 註冊之後
     *
     */
	public function afterRegister($user, $type_id)
	{
        $user_id = $user->id;

		//驗證碼
		$authCode = md5(uniqid().time());
		//$auth_url = config('api.frontend.user.verify').$checked_auth;

        $code = $this->num2alpha($user_id);

		$this->user->where('id', '=', $user_id)->update([
			'code'			=>	$code,
			'checked_auth'	=>	$authCode,
			'checked'	    =>	0,
        ]);

        //mail
        Mail::to($user->email, $user->name)
            ->send(new Mailer([
                'type_id'   =>  $type_id,
                'data'      =>  [
                    'user'      =>  $user,
                ],
            ]));
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
        Mail::to($user->email, $user->name)
            ->queue(new Mailer([
                'type_id'   =>  6,
                'data'      =>  [
                    'user'      =>  $user,
                    'password'  =>  $password,
                ],
            ]));
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

			$data = [
				'name'			=>	$name,
				'email'			=>	$email,
				'gender'		=>	$gender,
				'birthday'		=>	$birthday,
				'description'	=>	$description,
            ];

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