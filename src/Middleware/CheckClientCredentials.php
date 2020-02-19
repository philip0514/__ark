<?php

namespace Philip0514\Ark\Middleware;

use Laravel\Passport\Http\Middleware\CheckClientCredentials as CheckCredentials;
use Illuminate\Auth\AuthenticationException;

class CheckClientCredentials extends CheckCredentials
{
    /**
     * Validate token credentials.
     *
     * @param  \Laravel\Passport\Token  $token
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function validateCredentials($token)
    {
        if (! $token) {
            throw new AuthenticationException;
        }
    }
}
