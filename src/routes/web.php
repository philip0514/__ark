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

    /**
     *  User
     */
    //social
    $route->get('user/facebook', 'UserController@facebook')->name('user_facebook');
    $route->get('user/google', 'UserController@google')->name('user_google');

    //login
    $route->get('login', 'UserController@login')->name('login');
    $route->post('login/process', 'UserController@loginProcess')->name('login_process');
    $route->get('logout', 'UserController@logout')->name('logout');

    //register
    $route->get('register', 'UserController@register')->name('register');
    $route->post('register/validate', 'UserController@registerValidate')->name('register_validate');
    $route->post('register/process', 'UserController@registerProcess')->name('register_process');
    $route->get('register/completed', 'UserController@registerCompleted')->name('register_completed');

    //forgot password
    $route->get('forgot_password', 'UserController@forgot_password')->name('forgot_password');
    $route->post('forgot_password/process', 'UserController@forgotPasswordProcess')->name('forgot_password_process');
    $route->get('forgot_password/completed', 'UserController@forgot_password_completed')->name('forgot_password_completed');

    //info
    $route->get('user/info', 'UserController@info')->name('user_info');
    $route->get('user/info/completed', 'UserController@info_completed')->name('user_info_completed');

    //verify
    $route->get('user/verify', 'UserController@verify')->name('user_verify');
    $route->get('user/verify/completed', 'UserController@verify_completed')->name('user_verify_completed');
    $route->get('user/verify/failed', 'UserController@verify_failed')->name('user_verify_failed');

    //order
    $route->get('user/order', 'UserController@order')->name('user_order');

    /**
     *  Search
     */
    $route->get('search', 'SearchController@index')->name('search');

    /**
     *  Page
     */
    $route->get('/{page}', 'PageController')->name('page');
});
