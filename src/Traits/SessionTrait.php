<?php

namespace Philip0514\Ark\Traits;

use URL;

trait SessionTrait
{
    public function setReferralUrl()
    {
        if (!session()->has('referralUrl')) {
            $url = URL::previous();
            if (strpos($url, '/api/') !== false) {
                $url = route('index');
            }
            session()->put('referralUrl', $url);
        }
    }

    public function getReferralUrl($route = 'index', $parameters = [])
    {
        $url = route($route, $parameters);
        if (session()->has('referralUrl')) {
            if (strpos(session('referralUrl'), '/api/') === false) {
                $url = session('referralUrl');
            }
            session()->forget('referralUrl');
        }
        return $url;
    }
}