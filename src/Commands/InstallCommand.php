<?php

namespace Philip0514\Ark\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Philip0514\Ark\Traits\Seedable;
use Philip0514\Ark\ArkServiceProvider;

class InstallCommand extends Command
{
    use Seedable;

    protected $seedersPath = __DIR__.'/../../publishable/databases/seeds/';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'ark:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Ark Dashboard package';

    protected function getOptions()
    {
        return [
            ['demo', null, InputOption::VALUE_NONE, 'Install with demo data', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    public function fire(Filesystem $filesystem)
    {
        return $this->handle($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Publishing the Ark assets, database, and config files');

        // Publish only relevant resources on install
        $tags = ['seeds', 'config', 'ark', 'packages'];

        $this->call('vendor:publish', ['--provider' => ArkServiceProvider::class, '--tag' => $tags]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $this->info('Install Laravel Passport');
        $this->call('passport:install');

        $this->info('Seeding data into the database');
        $this->seed('ArkDatabaseSeeder');

        $demo = $this->option('demo');
        if($demo){
            $tags = ['media'];
            $this->call('vendor:publish', ['--provider' => ArkServiceProvider::class, '--tag' => $tags]);

            $this->info('Seeding Demo Data into the database');
            $this->seed('DemoDatabaseSeeder');
        }

        $this->info('Dumping the autoloaded files and reloading all new files');

        $composer = $this->findComposer();

        $process = new Process($composer.' dump-autoload');
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

        $process = new Process($composer.' install');

        /*
        $this->info('Setting up the hooks');
        $this->call('hook:setup');
        */

        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->info('Successfully installed Ark! Enjoy');
    }
}
