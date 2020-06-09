<?php

namespace Philip0514\Ark\Services;

use GuzzleHttp\Client;
use Cookie;
use Storage;
use Jenssegers\Agent\Agent;

class ClientTokenService
{
    static public function generate()
    {
        $agent = new Agent();
        if($agent->isRobot() && Storage::disk('local')->has(date('Y/m/d').'/token')) return;

        $client = new Client(['base_uri' => config('ark.api.url')]);
        $response = $client->request('POST', config('ark.api.url').'/oauth/token', [
            'headers'        => [
                'Content-Type'  => 'application/x-www-form-urlencoded',
                'Accept'        => config('ark.api.version'),
            ],
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => config('ark.api.client.id'),
                'client_secret' => config('ark.api.client.secret'),
            ]
        ]);
        $json = json_decode($response->getBody()->getContents());

        if($agent->isRobot()) {
            Storage::disk('local')->put(date('Y/m/d').'/token', $json->data->token->access_token);
        } else {
            session()->put('client_token', $json->data->token->access_token);
            session()->put('client_token_expires', $json->data->token->expires_in);
        }
    }
}