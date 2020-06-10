<?php
namespace Philip0514\Ark\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Philip0514\Ark\Services\ClientTokenService as ClientToken;
//use App\Services\ApiCacheService as ApiCache;
use Cookie;
use App;
//use Crypt;
use Log;
use Storage;

class Base
{
    protected $client;
    protected $access_token;
    protected $urls = [];
    protected $settings = [];

    protected function setting()
    {
        $this->settings = [
            'base_uri' => config('ark.api.url'),
            'version' => config('ark.api.version'),
        ];
    }

    public function __construct()
    {
        $this->setting();
        $this->client = new Client(['base_uri' => $this->settings['base_uri']]);
    }

    public function __call($function, $args)
    {
        $method = $this->urls[$function]['method'];
        $api = $this->urls[$function]['api'];
        $cache = $this->urls[$function]['cache'] ?? 0;
        if($args) {
            foreach ($args[0] as $key => $value) {
                $api = str_replace('{'.$key.'}', $value, $api);
            }   
        }
        $arrayType = ($method == 'GET') ? 'query' : 'form_params';
        $array[$arrayType] = isset($this->urls[$function]['values']) ? $this->urls[$function]['values'] : [];
        foreach ($array[$arrayType] as $key => $value) {
            if(isset($args[0][$key])) $array[$arrayType][$key] = $args[0][$key];
            if($array[$arrayType][$key] === null) unset($array[$arrayType][$key]);
        }
        $array = $this->beforeRequest($array);
        $send = (isset($args[0]['async']) && $args[0]['async']) ? 'requestAsync' : 'request';
        $returnError = isset($args[0]['returnError']) ? $args[0]['returnError'] : false;

        return $this->$send($method, $api, $array, $returnError, $cache);
    }

    protected function get_access_token()
    {
        return session()->get('password_token') ?? Cookie::get('password_token') ?? Cookie::get('client_token') ?? session()->get('client_token');   
    }

    public function beforeRequest($array = [])
    {
        $this->access_token = $this->get_access_token();
        if($this->access_token == null) $this->access_token = Storage::disk('local')->get(date('Y/m/d').'/token');
        if(!isset($array['headers'])) $array['headers'] = []; 
        if(!isset($array['headers']['Content-Type'])) $array['headers']['Content-Type'] = 'application/x-www-form-urlencoded';
        if(!isset($array['headers']['Authorization'])) $array['headers']['Authorization'] = 'Bearer '.$this->access_token;
        if(!isset($array['headers']['Accept'])) $array['headers']['Accept'] = $this->settings['version'];
        if(!isset($array['query'])) $array['query'] = [];
        $array['query']['lang'] = App::getLocale();
        return $array;
    }

    public function requestAsync($method = 'GET', $api, $array = [], $returnError = false, $cache = false)
    {
        return $this->client->requestAsync($method, $api, $array)->then(
            function (ResponseInterface $response) use ($method, $api, $array, $cache) {
                $contents = $response->getBody()->getContents();
                $json = json_decode($contents, true);
                return $json;
            },
            function (RequestException $e) use ($method, $api, $array, $returnError) {
                $response = $e->getResponse();
                return $this->errorHandler('requestAsync', $response, $method, $api, $array, $returnError);
            }
        );
    }

    public function request($method = 'GET', $api, $array = [], $returnError = false, $cache = false)
    {
        try {
            $response = $this->client->request($method, $api, $array);
            $contents = $response->getBody()->getContents();
            $json = json_decode($contents, true);
            return $json;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                return $this->errorHandler('request', $response, $method, $api, $array, $returnError);
            }
        }
    }

    public function errorHandler($type, $response, $method, $api, $array, $returnError) 
    {
        $contents = $response->getBody()->getContents();
        $json = json_decode($contents, true);
        if($returnError) return $json;
        switch ($json['status_code']) {
            case 404:
                if(config('app.debug')) return abort($json['status_code'], $api.' : '.$contents);
                else return abort($json['status_code']);
                break;
            case 500:
                if($json['message'] != "Unauthenticated.") {
                    if(config('app.send_error_to_slack')) Log::critical('API: '. $api ."\n". $contents);
                    return $json;
                }
                return $this->checkClientTokenAuthenticate($type, $json, $method, $api, $array, $contents, $returnError);
                break;
            default:
                return $json;
                break;
        }
    }

    private function checkClientTokenAuthenticate($type, $json, $method, $api, $array, $contents, $returnError)
    {
        if(Cookie::get('password_token')) {
            try {
                $response = $this->client->request('GET', 'article/recommend', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Bearer '.Cookie::get('password_token'),
                        'Accept' => $this->settings['version']
                    ],
                    'query' => [
                        'page' => 1,
                        'limit' => 1
                    ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    session()->flush();
                    Cookie::queue(Cookie::forget('client_token'));
                    Cookie::queue(Cookie::forget('password_token'));
                    return abort($json['status_code'], 'cookie unauthenticated');
                }
            }
            return $json;
        }
        if(Cookie::get('ct')) {
            try {
                $response = $this->client->request('GET', 'article/recommend', [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => 'Bearer '.Cookie::get('client_token'),
                        'Accept' => $this->settings['version']
                    ],
                    'query' => [
                        'page' => 1,
                        'limit' => 1
                    ]
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    Cookie::queue(Cookie::forget('client_token'));
                    ClientToken::generate();
                    $array['headers']['Authorization'] = 'Bearer '.session()->get('client_token');
                }
            }

            try {
                $response = $this->client->request($method, $api, $array);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    return abort(500, 'unauthenticated');
                }
            }
            $contents = $response->getBody()->getContents();
            $response = json_decode($contents, true);
            return $response;
        }
        return $json;
    }
}