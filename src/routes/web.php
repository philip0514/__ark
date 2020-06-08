<?php

$route = app('Illuminate\Routing\Router');

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Web',
], function($route){
    $route->get('/', 'WelcomeController@index')->name('index');
    $route->get('/login', 'UserController@login')->name('login');
    $route->get('/register', 'UserController@register')->name('register');
    $route->get('/forgot_password', 'UserController@forgot_password')->name('forgot_password');

    //bottom
    $route->get('/{page}', 'PageController')->name('page');
});
