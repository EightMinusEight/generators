<?php

namespace EightMinusEight\Generators;

use Illuminate\Filesystem\Filesystem;
use EightMinusEight\Generators\MigrationGenerator;
use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    	// Pulish the config file when user hits artisan vendor:publish
	    $this->publishes([
		    __DIR__ . '/config/generators.php' => config_path('generators.php'),
	    ]);


	    // Register Console Command
	    $this->commands([
	    	Commands\MakeScaffoldCommand::class
	    ]);


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

		// Merge the local config file (config/config.php) with the
	    // published version in config/eightminuseight.php (if published with artisan)
	    $this->mergeConfigFrom(
		    __DIR__ . '/config/generators.php', 'eightminuseight'
	    );


    }
}
