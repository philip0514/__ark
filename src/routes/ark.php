<?php
use Philip0514\Ark\Repositories\Dashboard\AdministratorRepository;

$structure = AdministratorRepository::structure();

$route = app('Illuminate\Routing\Router');

//elfinder
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Dashboard',
    'prefix'    =>  'elfinder',
], function($route){
    $route->get('tinymce5', 'ElfinderController@showTinyMCE5')->name('elfinder.tinymce5');
});

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Dashboard',
    'prefix'    =>  config('ark.prefix'),
], function($route){
    $route->get('ark/js/route', 'DashboardController@showRoute')->name('ark.route');
});

//login
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Dashboard',
    'prefix'    =>  config('ark.prefix'),
], function($route){
    //login
    $route->match(['get', 'post'], 'login', 'DashboardController@login')->name('login');
    $route->get('logout', 'DashboardController@logout')->name('logout');
});

//ajax route
$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Dashboard',
    'prefix'    =>  config('ark.prefix'),
], function($route){
    $route->post('request/toggle_sidebar', 'RequestController@toggleSidebar')->name('request.toggle_sidebar');
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
});

$route->group([
    'namespace' =>  '\Philip0514\Ark\Controllers\Dashboard',
    'prefix'    =>  config('ark.prefix'),
    'middleware'    =>  [
        '\Philip0514\Ark\Middleware\Url',
        '\Philip0514\Ark\Middleware\Permission',
    ],
], function($route){
    //dashboard
    $route->match(['get', 'post'], 'profile', 'AdministratorController@profile')->name('administrator.profile');
});

for($i=0; $i<sizeof($structure); $i++){
    $item = $structure[$i];

    if($item['url']=='dashboard'){
        $route->group([
            'namespace' =>  $item['namespace'],
            'prefix'    =>  config('ark.prefix'),
            'middleware'    =>  [
                '\Philip0514\Ark\Middleware\Url',
                '\Philip0514\Ark\Middleware\Permission',
            ],
        ], function($route){
            //dashboard
            $route->get('/', 'DashboardController@index')->name('dashboard');
        });
        continue;
    }

    $route->group([
        'namespace' =>  $item['namespace'],
        'prefix'    =>  config('ark.prefix'),
    ], function($route) use ($item){
        $route->match(['get', 'post'], sprintf('%s/validate', $item['url']), $item['controller'].'@validate')->name(sprintf('%s.validate', $item['url']));
        $route->get(sprintf('%s/datatable', $item['url']), $item['controller'].'@datatable')->name(sprintf('%s.datatable', $item['url']));
        $route->post(sprintf('%s/action', $item['url']), $item['controller'].'@action')->name(sprintf('%s.action', $item['url']));
        $route->post(sprintf('%s/columnVisible', $item['url']), $item['controller'].'@columnVisible')->name(sprintf('%s.columnVisible', $item['url']));
        $route->post(sprintf('%s/columnReorder', $item['url']), $item['controller'].'@columnReorder')->name(sprintf('%s.columnReorder', $item['url']));
        $route->post(sprintf('%s/rowReorder', $item['url']), $item['controller'].'@rowReorder')->name(sprintf('%s.rowReorder', $item['url']));
    });

    $route->group([
        'namespace' =>  $item['namespace'],
        'prefix'    =>  config('ark.prefix'),
        'middleware'    =>  [
            '\Philip0514\Ark\Middleware\Url',
            '\Philip0514\Ark\Middleware\Permission',
        ],
    ], function($route) use ($item){
        $route->match(['get', 'post', 'delete'], sprintf('%s', $item['url']), $item['controller'].'@index')->name(sprintf('%s.index', $item['url']));
        $route->match(['get', 'post'], sprintf('%s/create', $item['url']), $item['controller'].'@single')->name(sprintf('%s.create', $item['url']));
        $route->match(['get', 'post'], sprintf('%s/{id}', $item['url']), $item['controller'].'@single')->name(sprintf('%s.update', $item['url']))->where('id', '[0-9]+');
        $route->get(sprintf('%s/{id}/view', $item['url']), $item['controller'].'@single')->name(sprintf('%s.read', $item['url']))->where('id', '[0-9]+');
    });
}