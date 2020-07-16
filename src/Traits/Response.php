<?php

namespace Philip0514\Ark\Traits;

/**
 * Trait Respond
 *
 * @package App\Traits
 */
trait Response
{
    protected function responseError($error, $outout='json')
    {
        switch($error){
            // invalid
            case 'invalid_credentials':
            case 'invalid_client':
            case 'invaild_login':
            case 'invalid_refresh_token':

            // required
            case 'token_required':
            case 'register_required':
            case 'refresh_token_required':
            case 'facebook_required':
            case 'google_required':
            case 'twitter_required':
            case 'verification_required':
            case 'verification_checked':
            case 'forgot_password_required':
            case 'user_required':

            //existed
            case 'user_existed':
                $code = 400;
                $data = [
                    "info"      =>  $error,
                    "message"   =>  config('api.response.'.$error),
                ];
            break;
            case 'user_404':
            case 'tag_404':
                $code = 404;
                $data = [
                    "info"      =>  $error,
                    "message"   =>  config('api.response.'.$error),
                ];
            break;
            default:
                $code = 500;
                $data = [
                    "info"      =>  'server_error',
                    'message'   =>  'Internal server error',
                    "hint"      =>  $error,
                ];
            break;
        }

        $result = [
            'success'       =>  false,
            'status_code'	=>	$code,
            'error'			=>	$data,
            'data'			=>	null,
        ];

		return response()->json($result, 200);
    }

    protected function responseSuccess($rows1=null, $code=200, $outout='json')
    {
        $data = isset($rows1['data']) ? $rows1['data'] : null;
        $data = is_array($data) ? $data : [];
        $data = sizeof($data) ? $data : null;

        $html = isset($rows1['html']) ? $rows1['html'] : null;
        $pagination = isset($rows1['pagination']) ? $rows1['pagination'] : null;
       
        $result = [
            'success'       =>  true,
            'status_code'	=>	$code,
            'error'			=>	null,
            'data'			=>	$data,
            'pagination'    =>  $pagination,
            'html'          =>  $html,
        ];

        if(!$html){
            unset($result['html']);
        }

        if(!$pagination){
            unset($result['pagination']);
        }

		return response()->json($result, 200);
    }

    protected function pagination($result)
    {
		return [
			'total'			=>	(int)$result->total(),
			'per_page'		=>	(int)$result->perPage(),
			'current_page'	=>	(int)$result->currentPage(),
			'last_page'		=>	(int)$result->lastPage(),
			'next_page_url'	=>	$result->nextPageUrl(),
			'prev_page_url'	=>	$result->previousPageUrl(),
		];
    }
}