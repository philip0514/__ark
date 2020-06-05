<?php

$route = app('Illuminate\Routing\Router');

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Web',
], function($route){
    $route->get('/', 'WelcomeController@index')->name('index');
});
