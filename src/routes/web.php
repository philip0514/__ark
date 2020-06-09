<?php

$route = app('Illuminate\Routing\Router');

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Web',
    'middleware'    =>  [
        '\Philip0514\Ark\Middleware\ClientTokenGet',
        '\Philip0514\Ark\Middleware\ClientTokenSet',
    ],
], function($route){
    $route->get('/', 'WelcomeController@index')->name('index');
    $route->match(['get', 'post'], 'login', 'UserController@login')->name('login');
    $route->match(['get', 'post'], 'register', 'UserController@register')->name('register');
    $route->get('register/completed', 'UserController@register_completed')->name('register_completed');

    $route->match(['get', 'post'], 'forgot_password', 'UserController@forgot_password')->name('forgot_password');
    $route->get('forgot_password/completed', 'UserController@forgot_password_completed')->name('forgot_password_completed');
    $route->get('user/facebook', 'UserController@facebook')->name('user_facebook');
    $route->get('user/google', 'UserController@google')->name('user_google');
    $route->get('user/info', 'UserController@info')->name('user_info');
    $route->get('user/info/completed', 'UserController@info_completed')->name('user_info_completed');
    $route->get('user/verify', 'UserController@verify')->name('user_verify');
    $route->get('user/verify/completed', 'UserController@verify_completed')->name('user_verify_completed');
    $route->get('user/verify/failed', 'UserController@verify_failed')->name('user_verify_failed');

    $route->get('search', 'SearchController@index')->name('search');

    //bottom
    $route->get('/{page}', 'PageController')->name('page');
});
