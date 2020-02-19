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
     * @param $request
     * @return void
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
     * @param $request
     * @return $result
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

            $data['user'] = [
                'id'			=>	$user->id,
                'name'			=>	$user->name,
                'email'			=>	$user->email,
                'facebook_id'	=>	$user->facebook_id,
                'google_id'		=>	$user->google_id,
                'twitter_id'	=>	$user->twitter_id,
                'gender'		=>	$user->gender,
                'birthday'		=>	$user->birthday,
            ];

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
     * @param $request
     * @return $result
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
     * @param $request
     * @return $result
     */
    public function register($request)
    {
        try{
            $client_id = $request->input('client_id', $this->client_id);
            $email = $request->input('email');
            $password = $request->input('password');
            $name = $request->input('name');

            if(!$email || !$password || !$name){
                throw new Exception('register_required');
            }

            $user = $this->user->where('email', '=', $email)->select('id')->first();
            
            if(isset($user->id) && $user->id){
                throw new Exception('user_existed');
            }

            //密碼加密
            $password_first = mb_substr($password, 0, 3);
            $password_after = mb_substr($password, 3);
            $password_hidden = $password_first.str_repeat("*", mb_strlen($password_after)); 

            //註冊
            $rows1 = array(
                'email'			=>	$email,
                'password'		=>	Hash::make($password),
                'name'			=>	$name,
                'created_at'	=>	Carbon::now(),
                'updated_at'	=>	Carbon::now(),
            );
            $user_id = $this->user->insertGetId($rows1);

            //登入
            $user = $this->user->find($user_id);
            $data = $this->getBearerTokenByUser($user, $client_id, false);
            $code = $this->num2alpha($user_id);

            $data['user'] = array(
                'id'		        =>	$user->id,
                'name'		        =>	$user->name,
                'email'		        =>	$user->email,
                'code'		        =>	$code,
                'avatar'	        =>	null,
                'password_hidden'   =>  $password_hidden,
            );

            return $data;
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
     * @param $request
     * @return $result
     */
	public function after_register($data=null, $email_type='default')
	{
        $user_id = $data['id'];
		$name = $data['name'];
		$email = $data['email'];
		$password = isset($data['password']) ? $data['password'] : null;

		//驗證碼
		$auth_code = md5(uniqid().time());
		$auth_url = config('api.frontend.user.verify').$auth_code;

        $code = $this->num2alpha($user_id);

		$this->user->where('id', '=', $user_id)->update([
			'code'			=>	$code,
			'auth_code'		=>	$auth_code,
			'auth_checked'	=>	0,
        ]);

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
					'password'	=> $password,
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
     * @param $request
     * @return $result
     */
    public function facebook($request)
    {
        try{
            $client_id = $request->input('client_id', $this->client_id);
            $email = $request->input('email');
            $name = $request->input('name');
            $facebook_id = $request->input('facebook_id');
            $password = implode(',', array($email, $facebook_id, time(), uniqid()));

            if(!$facebook_id){
                throw new Exception('facebook_required');
            }

            $user = $this->user
                    ->select('id', 'email', 'facebook_id', 'name', 'avatar', 'code', 'language', 'twitter_token', 'updated_at')
                    ->where('facebook_id', '=', $facebook_id)
                    ->where('display', 1)
                    ->first();

            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('facebook_required');
                }

                $user = $this->user
                        ->where('email', '=', $email)
                        ->select('id', 'email', 'facebook_id', 'name', 'avatar', 'code', 'updated_at')
                        ->first();

                if($user){
                    //若email存在，則寫入 facebook_id
                    $this->user->where('id', $user->id)
                         ->update(['facebook_id'	=>	$facebook_id]);
                }else{
                    $rows1 = array(
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'facebook_id'	=>	$facebook_id,
                        'created_at'	=>	Carbon::now(),
                        'updated_at'	=>	Carbon::now(),
                    );
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $rows1 = array(
                        'id'			=>	$user_id,
                        'name'			=>	$name,
                        'email'			=>	$email,
                    );
                    //$this->after_register($rows1, 'facebook');

                    $user = $this->user->find($user_id);
                }
            }

            //登入
            $data = $this->getBearerTokenByUser($user, $client_id, false);

            $data['user'] = array(
                'id'		=>	$user->id,
                'name'		=>	$user->name,
                'email'		=>	$user->email,
                'code'		=>	$user->code,
                'avatar'	=>	$user->avatar ? config('api.s3.avatar.url').$user->avatar.'?t='.strtotime($user->updated_at) : null,
                'language'  =>  $user->language,
                'twitter_token'     =>  $user->twitter_token ? json_decode($user->twitter_token, true) : null,
            );

            return $data;
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
     * @param $request
     * @return $result
     */
    public function google($request)
    {
        try{
            $client_id = $request->input('client_id', $this->client_id);
            $email = $request->input('email');
            $name = $request->input('name');
            $google_id = $request->input('google_id');
            $password = implode(',', array($email, $google_id, time(), uniqid()));

            if(!$google_id){
                throw new Exception('google_required');
            }

            $user = $this->user
                    ->select('id', 'email', 'google_id', 'name', 'avatar', 'code', 'language', 'twitter_token', 'updated_at')
                    ->where('google_id', '=', $google_id)
                    ->where('display', 1)
                    ->first();

            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('google_required');
                }

                $user = $this->user
                        ->where('email', '=', $email)
                        ->select('id', 'email', 'google_id', 'name', 'avatar', 'code', 'updated_at')
                        ->first();

                if($user){
                    //若email存在，則寫入 google_id
                    $this->user->where('id', $user->id)
                         ->update(['google_id'	=>	$google_id]);
                }else{

                    $rows1 = array(
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'google_id'	    =>	$google_id,
                        'created_at'	=>	Carbon::now(),
                        'updated_at'	=>	Carbon::now(),
                    );
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $rows1 = array(
                        'id'			=>	$user_id,
                        'name'			=>	$name,
                        'email'			=>	$email,
                    );
                    //$this->after_register($rows1, 'google');

                    $user = $this->user->find($user_id);
                }
            }

            //登入
            $data = $this->getBearerTokenByUser($user, $client_id, false);
            
            $data['user'] = array(
                'id'		=>	$user->id,
                'name'		=>	$user->name,
                'email'		=>	$user->email,
                'code'		=>	$user->code,
                'avatar'	=>	$user->avatar ? config('api.s3.avatar.url').$user->avatar.'?t='.strtotime($user->updated_at) : null,
                'language'  =>  $user->language,
                'twitter_token'     =>  $user->twitter_token ? json_decode($user->twitter_token, true) : null,
            );

            return $data;
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
     * @param $request
     * @return $result
     */
    public function twitter($request)
    {
        try{
            $client_id = $request->input('client_id', $this->client_id);
            $email = $request->input('email');
            $name = $request->input('name');
            $twitter_id = $request->input('twitter_id');
            $password = implode(',', array($email, $twitter_id, time(), uniqid()));

            if(!$twitter_id){
                throw new Exception('twitter_required');
            }

            $user = $this->user
                    ->select('id', 'email', 'twitter_id', 'name', 'avatar', 'code', 'language', 'twitter_token', 'updated_at')
                    ->where('twitter_id', '=', $twitter_id)
                    ->where('display', 1)
                    ->first();
                    
            if(!$user){
                //未註冊過
                if(!$email || !$name){
                    //確認是否有少email或name 如果有，則需要先填寫email and name之後 再次回傳給server
                    throw new Exception('twitter_required');
                }

                $user = $this->user->where('email', '=', $email)
                        ->select('id', 'email', 'twitter_id', 'name', 'avatar', 'code', 'updated_at')
                        ->first();

                if($user){
                    //若email存在，則寫入 twitter_id
                    $this->user
                        ->where('id', $user->id)
                        ->update(['twitter_id'	=>	$twitter_id]);
                }else{

                    $rows1 = array(
                        'email'			=>	$email,
                        'password'		=>	Hash::make($password),
                        'name'			=>	$name,
                        'twitter_id'	=>	$twitter_id,
                        'created_at'	=>	Carbon::now(),
                        'updated_at'	=>	Carbon::now(),
                    );
                    $user_id = $this->user->insertGetId($rows1);

                    //後續處理
                    $rows1 = array(
                        'id'			=>	$user_id,
                        'name'			=>	$name,
                        'email'			=>	$email,
                    );
                    //$this->after_register($rows1, 'twitter');

                    $user = $this->user->find($user_id);
                }
            }

            //登入
            $data = $this->getBearerTokenByUser($user, $client_id, false);

            $data['user'] = array(
                'id'		=>	$user->id,
                'name'		=>	$user->name,
                'email'		=>	$user->email,
                'code'		=>	$user->code,
                'avatar'	=>	$user->avatar ? config('api.s3.avatar.url').$user->avatar.'?t='.strtotime($user->updated_at) : null,
                'language'  =>  $user->language,
                'twitter_token'     =>  $user->twitter_token ? json_decode($user->twitter_token, true) : null,
            );

            return $data;
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
            ->where('auth_code', '=', $code)
            ->first();

        return $user;
    }

    public function authCodeUpdate($user_id)
    {
        $this->user
            ->where('id', '=', $user_id)
            ->update(['auth_checked'	=>	1]);
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
		$user = $this->user->where('auth_code', '=', $code)->first();

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
    }

    public function info_post($request)
    {
        try{
            $token = $this->parse_token($request);
            $token_id = $token['token_id'];
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
			$image = $request->file('avatar');
			$code = strip_tags($request->input('code'));
			$facebook = strip_tags($request->input('facebook'));
			$twitter = strip_tags($request->input('twitter'));
			$website = strip_tags($request->input('website'));
			$location = $request->input('location');
			$language = $request->input('language', null);

			if(!$name){
				throw new Exception('info_required');
			}

			$data = array(
				'name'			=>	$name,
				'email'			=>	$email,
				'gender'		=>	$gender,
				'birthday'		=>	$birthday,
				'description'	=>	$description,
				'facebook'	    =>	$facebook,
				'twitter'	    =>	$twitter,
				'website'	    =>	$website,
				'location'	    =>	$location,
				'language'		=>	$language,
			);

            $inviter_id = null;
			if($code){
                $inviter = $this->findByCode($code);

				if(!isset($inviter->id)){
					throw new Exception('inviter_404');
				}

				$data['inviter_id'] = $inviter_id = $inviter->id;
			}

			if($password){
				$data['password'] = Hash::make($password);
			}

			if(!$email){
				unset($data['email']);
			}

			//頭像上傳
			if($image){
				$file_name = $user_id.'.jpg';
				$file_path = config('api.s3.avatar.folder').$file_name;
				$image = (string) Image::make($image)->fit(800, 800)->encode('jpg', 75);
				$image_url = config('api.s3.domain').$file_path;

				//上傳至s3
				$s3 = \Storage::disk('s3');
				$s3->put($file_path, $image, 'public');	

				$data['avatar'] = $file_name.'?t='.time();

				Cloudflare::purge_cache([$image_url]);
			}

            $this->user->where('id', '=', $user_id)->update($data);

            return [
                'inviter_id'    =>  $inviter_id
            ];
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    public function avatar($request)
    {
        try{
            $token = $this->parse_token($request);
            $token_id = $token['token_id'];
            $user_id = $token['user_id'];

			if(!$user_id){
				throw new Exception('user_404');
            }

            $image = $request->file('avatar');
            $image_url = null;

            if($image){
                $file_name = $user_id.'.jpg';
                $file_path = config('api.s3.avatar.folder').$file_name;

                $image = Image::make($image);
                $width = $image->width();
                $height = $image->height();
                if($width>=800 && $height>=800){
                    $image = $image->fit(800, 800);
                }
                $image = (string) $image->encode('jpg', 75);

                //$image = (string) Image::make($image)->fit(800, 800)->encode('jpg', 75);
                $image_url = config('api.s3.domain').$file_path;

                //上傳至s3
                $s3 = \Storage::disk('s3');
                $s3->put($file_path, $image, 'public');	

                $data['avatar'] = $file_name;

                Cloudflare::purge_cache([$image_url]);

                $this->user->where('id', '=', $user_id)->update($data);
            }else{
                throw new Exception('avatar_required');
            }

            return [
                'image_url' =>  $image_url
            ];
        }
        catch (Exception $e) {
            $result = [
                'error'     =>  $e->getMessage()
            ];
            return $result;
        }
    }

    public function background($request)
    {
        try{
            $token = $this->parse_token($request);
            $token_id = $token['token_id'];
            $user_id = $token['user_id'];

            if(!$user_id){
                throw new Exception('user_404');
            }

            $image = $request->file('background');
            $image_url = null;

            if($image){
                $file_name = $user_id.'.jpg';
                $file_path = config('api.s3.background.folder').$file_name;
                //$image = (string) Image::make($image)->fit(800, 800)->encode('jpg', 75);
                $image = (string) Image::make($image)->encode('jpg', 75);
                $image_url = config('api.s3.domain').$file_path;

                //上傳至s3
                $s3 = \Storage::disk('s3');
                $s3->put($file_path, $image, 'public'); 

                $data['background'] = $file_name;

                Cloudflare::purge_cache([$image_url]);

                $this->user->where('id', '=', $user_id)->update($data);
            }else{
                throw new Exception('background_required');
            }

            return [
                'image_url' =>  $image_url
            ];
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
        $id = (new Parser())->parse($value)->getHeader('jti');

        if($id){
            $token = DB::table('oauth_access_tokens')
                ->where('id', '=', $id)
                ->update(['revoked' => true]);

            return  true;
        }

        return false;
    }

    public function searchHistoryUpdate($user_id, $history)
    {
        $this->user
        ->where('id', '=', $user_id)
        ->update(['search_history'	=>	$history]);
    }

    public function search($name, $start=0, $limit=10, $user_id=0)
    {
		$conversion = new Zhconversion();

		$text_t = $conversion->tt($name);
        $text_s = $conversion->ts($name);

        $result = $this->user
			->where(function ($query) use ($text_t, $text_s) {
				$query->where('name', 'like', '%'.$text_t.'%')
					->orWhere('name', 'like', '%'.$text_s.'%');
			})
            ->where('display', 1)
            ->orderBy('name', 'asc');

        $count = $result->count();
        $result = $result->offset($start)->limit($limit)->get()->toArray();

        $user_following = [];
        if($user_id){
            $user = $this->user->find($user_id);
            $rows1 = $user->following()->select('users.id')->get()->toArray();

            for($i=0; $i<sizeof($rows1); $i++){
                $user_following[] = $rows1[$i]['id'];
            }
        }
        for($i=0; $i<sizeof($result); $i++){
            $result[$i]['followed'] = false;
            if(in_array($result[$i]['id'], $user_following)){
                $result[$i]['followed'] = true;
            }
        }

        return [
            'count'     =>  $count,
            'result'    =>  $result,
        ];
    }

    private function created_time()
    {
        //更新原本沒有created_at的時間
        $rows1 = $this->mail->get()->toArray();
        for($i=0; $i<sizeof($rows1); $i++){
            $this->user->where('id', $rows1[$i]['user_id'])->whereNULL('created_at')->update(['created_at'    =>  $rows1[$i]['created_at']]);
        }
        $this->user->whereNULL('created_at')->update(['created_at'    =>  '2018-12-01 00:00:00']);
    }

    public function followAction($user_id, $follow_id)
    {
        $user = $this->user->find($user_id);

        $rows1 = $user->following()->select('users.id')->where('user_relations_id', $follow_id)->first();

		if($rows1){
            $status = false;
            $user->following()->detach($follow_id);

            $user->notify()->detach([
                $follow_id
            ]);
        }else{
            $status = true;
            $user->following()->attach($follow_id);

            $user->notify()->attach([
                $follow_id	=>	[
                    'type'			=>	'follow',
                    'user_from'		=>	$user_id,
                ]
            ]);
        }

        return $status;
    }

    public function followed($user_id, $start=0, $limit=10, $viewer_id=null)
    {
        $user = $this->user->find($user_id);
        $rows1 = $user->followed();

        $count = $rows1->count();
        $result = $rows1->offset($start)->limit($limit)->get()->toArray();

        $viewer_following = [];
        if($viewer_id){
            $viewer = $this->user->find($viewer_id);
            $rows1 = $viewer->following()->select('users.id')->get()->toArray();

            for($i=0; $i<sizeof($rows1); $i++){
                $viewer_following[] = $rows1[$i]['id'];
            }
        }
        for($i=0; $i<sizeof($result); $i++){
            $result[$i]['followed'] = false;
            if(in_array($result[$i]['id'], $viewer_following)){
                $result[$i]['followed'] = true;
            }
        }

        return [
            'count'     =>  $count,
            'result'    =>  $result,
        ];
    }

    public function following($user_id, $start=0, $limit=10, $viewer_id=null)
    {
        $user = $this->user->find($user_id);
        $rows1 = $user->following();

        $count = $rows1->count();
        $result = $rows1->offset($start)->limit($limit)->get()->toArray();

        $viewer_following = [];
        if($viewer_id){
            $viewer = $this->user->find($viewer_id);
            $rows1 = $viewer->following()->select('users.id')->get()->toArray();

            for($i=0; $i<sizeof($rows1); $i++){
                $viewer_following[] = $rows1[$i]['id'];
            }
        }
        for($i=0; $i<sizeof($result); $i++){
            $result[$i]['followed'] = false;
            if(in_array($result[$i]['id'], $viewer_following)){
                $result[$i]['followed'] = true;
            }
        }

        return [
            'count'     =>  $count,
            'result'    =>  $result,
        ];
    }

    public function viewed($user_id, $start=0, $limit=10)
    {
        $user = $this->user->find($user_id);

        $result = $user->articles();

        $count = $result->count();
        $rows1 = $result->orderBy('article_user.updated_at', 'desc')->offset($start)->limit($limit)->get()->toArray();

        $article_id = [];
        for($i=0; $i<sizeof($rows1); $i++){
            $article_id[] = $rows1[$i]['id'];
        }

        return [
            'count'         =>  $count,
            'article_id'    =>  $article_id,
        ];
    }

    public function liked($user_id, $type=0, $start=0, $limit=10)
    {
        $article_id = $comment_id = [];
        $user = $this->user->find($user_id);

        switch($type){
            default:
            case 0:
                $result = $user->likedArticles();

                $count = $result->count();
                $rows1 = $result->orderBy('article_user.updated_at', 'desc')->offset($start)->limit($limit)->get()->toArray();

                for($i=0; $i<sizeof($rows1); $i++){
                    $article_id[] = $rows1[$i]['id'];
                }
            break;
            case 1:
                $result = $user->likedComments();

                $count = $result->count();
                $rows1 = $result->orderBy('comment_user.updated_at', 'desc')->offset($start)->limit($limit)->get()->toArray();

                for($i=0; $i<sizeof($rows1); $i++){
                    $comment_id[] = $rows1[$i]['id'];
                }
            break;
        }

        return [
            'count'         =>  $count,
            'article_id'    =>  $article_id,
            'comment_id'    =>  $comment_id
        ];
    }

    public function followCount($user_id)
    {
        $user = $this->user->find($user_id);
        $followed = $user->followed()->count();
        $following = $user->following()->count();

        return [
            'followed'      =>  $followed,
            'following'     =>  $following,
        ];
    }

    public function is_followed($user_id, $target_id)
    {
        $user = $this->user->find($user_id);

        $rows1 = $user->following()->select('users.id')->where('user_relations_id', $target_id)->first();

		if($rows1){
            $status = true;
        }else{
            $status = false;
        }

        return $status;
    }

    public function twitter_setting($user_id, $url, $data)
    {
        $this->user->where('id', $user_id)->update([
            'twitter'           =>  $url,
            'twitter_setting'   =>  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        ]);
    }

    public function twitter_token_set($user_id, $token)
    {
        $this->user->where('id', $user_id)->update([
            'twitter_token'   	=>  $token
        ]);
    }

    public function twitter_token_get($user_id)
    {
		$rows1 = $this->user->select('id', 'twitter_token')->where('id', $user_id)->first()->toArray();

		return $rows1;
    }

    public function facebook_setting($user_id, $url, $data)
    {
        $this->user->where('id', $user_id)->update([
            'facebook'           =>  $url,
            'facebook_setting'   =>  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        ]);
    }

    public function privacy($user_id, $data)
    {
        $this->user->where('id', $user_id)->update([
            'privacy'   =>  json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        ]);
	}
}