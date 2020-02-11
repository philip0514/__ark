<?php
namespace Philip0514\Ark;
/*
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
*/
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Intervention\Image\ImageServiceProvider;

class ArkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/ark.php');

        $this->publishes([
            __DIR__.'/../publishable/config/ark.php' => config_path('ark.php'),
            __DIR__.'/../publishable/config/datatables.php' => config_path('datatables.php'),
            __DIR__.'/../publishable/config/elfinder.php' => config_path('elfinder.php'),
            __DIR__.'/../publishable/config/image.php' => config_path('image.php'),
            __DIR__.'/../publishable/config/permission.php' => config_path('permission.php'),
        ], 'config');

        /*
        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/app/provider.php', 'app.providers'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/app/alias.php', 'app.aliases'
        );
        */

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/guard.php', 'auth.guards'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/password.php', 'auth.passwords'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/provider.php', 'auth.providers'
        );

        $this->loadMigrationsFrom(__DIR__.'/..//publishable/databases/migrations');
    }

    // 註冊套件函式
    public function register()
    {
        //$this->app->register(ImageServiceProvider::class);
        //$this->app->register(PermissionServiceProvider::class);

        $this->app->singleton('ark', function ($app) {
            return new Ark();
        });
        
        if($this->app->runningInConsole()){
            
        }

        $this->registerPublishableResources();
    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'seeds' => [
                "{$publishablePath}/databases/seeds/" => database_path('seeds'),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }
}