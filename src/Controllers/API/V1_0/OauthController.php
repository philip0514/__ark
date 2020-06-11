<?php
namespace Philip0514\Ark\Controllers\API\V1_0;

use Laravel\Passport\Http\Controllers\AccessTokenController;
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
class OauthController extends AccessTokenController
{
	use Response;
	
	private $repo;

	private function init()
	{
		$this->repo = new \stdClass();
		$this->repo->user = new UserRepository();
	}

    public function token(ServerRequestInterface $request)
    {
		$this->init();
		$data = $request->getParsedBody();

		try {
			$grant_type = isset($data['grant_type']) ? $data['grant_type'] : null;
			$client_id = isset($data['client_id']) ? $data['client_id'] : null;
			$client_secret = isset($data['client_secret']) ? $data['client_secret'] : null;

			if(!$grant_type || !$client_id || !$client_secret){
				throw new Exception('token_required');
			}

			$serializer = new UserSerializer();
			switch($grant_type){
                case 'client_credentials':
                    $tokenResponse = $this->issueToken($request);
                    $content = $tokenResponse->getContent();
                    $data = json_decode($content, true);
                    $data = $serializer->clientToken($data);
				break;
				case 'password':
					$username = $data['username'];
					$password = isset($data['password']) ? $data['password'] : '';
					$user = $this->repo->user->password($username, $password);
					if(!$user){
						throw new Exception('invaild_login');
					}

					$tokenResponse = $this->issueToken($request);
					$content = $tokenResponse->getContent();
					$data = json_decode($content, true);
					$data['user'] = $user;
					$data = $serializer->passwordToken($data);
				break;
				case 'refresh_token':
					$refresh_token = isset($data['refresh_token']) ? $data['refresh_token'] : null;

					if(!$refresh_token){
						throw new Exception('refresh_token_required');
					}

					$tokenResponse = $this->issueToken($request);
					$content = $tokenResponse->getContent();
					$data = json_decode($content, true);

					if(isset($data['error'])){
						throw new Exception('invalid_refresh_token');
					}

					$data = $serializer->refreshToken($data);
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
}
