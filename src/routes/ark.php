<?php
use Philip0514\Ark\Repositories\AdministratorRepository;

$structure = AdministratorRepository::structure();

$route = app('Illuminate\Routing\Router');

//elfinder
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers',
    'prefix'    =>  'elfinder',
], function($route){
    $route->get('tinymce5', 'ElfinderController@showTinyMCE5')->name('elfinder.tinymce5');
});

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers',
    'prefix'    =>  config('ark.prefix'),
], function($route){
    $route->get('ark/js/route', 'DashboardController@showRoute')->name('ark.route');
});

//login
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers',
    'prefix'    =>  config('ark.prefix'),
], function($route){
    //login
    $route->match(['get', 'post'], 'login', 'DashboardController@login')->name('login');
    $route->get('logout', 'DashboardController@logout')->name('logout');
});

//ajax route
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers',
    'prefix'    =>  config('ark.prefix'),
], function($route) use ($structure){
    $route->post('request/toggle_sidebar', 'RequestController@toggle_sidebar')->name('request.toggle_sidebar');
    $route->post('request/zip', 'RequestController@zip')->name('request.zip');

    //media
    $route->post('media/manager', 'MediaController@manager')->name('media.manager');
    $route->post('media/upload', 'MediaController@upload')->name('media.upload');
    $route->get('media/data', 'MediaController@data')->name('media.data');
    $route->match(['get', 'post'], 'media/editor', 'MediaController@editor')->name('media.editor');

    //tag
    $route->get('tag/search', 'TagController@search')->name('tag.search');
    $route->post('tag/insert', 'TagController@insert')->name('tag.insert');

    //user
    $route->get('user/search', 'UserController@search')->name('user.search');

    for($i=0; $i<sizeof($structure); $i++){
        $route->match(['get', 'post'], sprintf('%s/validate', $structure[$i]['url']), $structure[$i]['controller'].'@validate')->name(sprintf('%s.validate', $structure[$i]['url']));
        $route->get(sprintf('%s/datatable', $structure[$i]['url']), $structure[$i]['controller'].'@datatable')->name(sprintf('%s.datatable', $structure[$i]['url']));
        $route->post(sprintf('%s/action', $structure[$i]['url']), $structure[$i]['controller'].'@action')->name(sprintf('%s.action', $structure[$i]['url']));
        $route->post(sprintf('%s/columnVisible', $structure[$i]['url']), $structure[$i]['controller'].'@columnVisible')->name(sprintf('%s.columnVisible', $structure[$i]['url']));
        $route->post(sprintf('%s/columnReorder', $structure[$i]['url']), $structure[$i]['controller'].'@columnReorder')->name(sprintf('%s.columnReorder', $structure[$i]['url']));
        $route->post(sprintf('%s/rowReorder', $structure[$i]['url']), $structure[$i]['controller'].'@rowReorder')->name(sprintf('%s.rowReorder', $structure[$i]['url']));
    }
});

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers',
    'prefix'    =>  config('ark.prefix'),
    'middleware'    =>  [
        '\Philip0514\Ark\Middleware\Url',
        '\Philip0514\Ark\Middleware\Permission',
    ],
], function($route) use ($structure){
    //dashboard
    //$route->get('/', 'WelcomeController@index')->name('index');
    $route->get('/', 'DashboardController@index')->name('dashboard');
    $route->match(['get', 'post'], 'profile', 'AdministratorController@profile')->name('administrator.profile');

    for($i=0; $i<sizeof($structure); $i++){
        $route->match(['get', 'post', 'delete'], sprintf('%s', $structure[$i]['url']), $structure[$i]['controller'].'@index')->name(sprintf('%s.index', $structure[$i]['url']));
        $route->match(['get', 'post'], sprintf('%s/create', $structure[$i]['url']), $structure[$i]['controller'].'@single')->name(sprintf('%s.create', $structure[$i]['url']));
        $route->match(['get', 'post'], sprintf('%s/{id}', $structure[$i]['url']), $structure[$i]['controller'].'@single')->name(sprintf('%s.update', $structure[$i]['url']));
        $route->get(sprintf('%s/{id}/view', $structure[$i]['url']), $structure[$i]['controller'].'@single')->name(sprintf('%s.read', $structure[$i]['url']));
        //$route->delete(sprintf('%s/{id}/delete', $structure[$i]['url']), $structure[$i]['controller'].'@single');
    }
});