<?php
namespace Philip0514\Ark;

use Illuminate\Support\ServiceProvider;

class ArkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/ark.php');

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/guard.php', 'auth.guards'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/password.php', 'auth.passwords'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/auth/provider.php', 'auth.providers'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../publishable/config/filesystems/disks.php', 'filesystems.disks'
        );

        $this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\Session\Middleware\StartSession');
        $this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware('Illuminate\View\Middleware\ShareErrorsFromSession');
    }

    // 註冊套件函式
    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'ark');

        $this->app->singleton('ark', function ($app) {
            return new Ark();
        });

        $this->registerHelpers();

        if ($this->app->runningInConsole()) {
            //$this->removePublishableResources();exit;
            $this->loadMigrationsFrom(__DIR__.'/../publishable/databases/migrations');
            $this->registerPublishableResources();
            $this->registerConsoleCommands();
        }
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
            'config'    =>  [
                "{$publishablePath}/config/ark.php" => config_path('ark.php'),
                "{$publishablePath}/config/datatables.php" => config_path('datatables.php'),
                "{$publishablePath}/config/elfinder.php" => config_path('elfinder.php'),
                "{$publishablePath}/config/image.php" => config_path('image.php'),
                "{$publishablePath}/config/permission.php" => config_path('permission.php'),
            ],
            'ark' => [
                "{$publishablePath}/assets/" => public_path('ark'),
            ],
            'packages'  =>  [
                "{$publishablePath}/packages/" => public_path('packages'),
            ]
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Register the commands accessible from the Console.
     */
    private function registerConsoleCommands()
    {
        $this->commands(Commands\InstallCommand::class);
    }

    private function registerHelpers()
    {
        // Load the helpers in app/Http/helpers.php
        if (file_exists($file = dirname(__DIR__).'/src/Helpers/autoload.php'))
        {
            require $file;
        }
    }

    private function removePublishableResources()
    {
        $rows1 = [
            config_path('ark.php'),
            config_path('datatables.php'),
            config_path('elfinder.php'),
            config_path('image.php'),
            config_path('permission.php'),
            database_path('seeds'),
            public_path('ark'),
        ];
        for($i=0; $i<sizeof($rows1); $i++){
            if(is_dir($rows1[$i])){
                $this->deleteDirectory($rows1[$i]);
            }else{
                unlink($rows1[$i]);
            }
        }
    }

    private function deleteDirectory($dirname)
    {
        if (is_dir($dirname)){
            $dir_handle = opendir($dirname);
        }
        if (!$dir_handle){
            return false;
        }
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file)){
                    unlink($dirname."/".$file);
                }else{
                    $this->deleteDirectory($dirname.'/'.$file);
                }
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
}
}