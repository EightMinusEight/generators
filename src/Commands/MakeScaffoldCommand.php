<?php

namespace EightMinusEight\Generators\Commands;


use EightMinusEight\Generators\MigrationGenerator;
use EightMinusEight\Generators\ScaffoldGenerator;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeScaffoldCommand extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $name = 'make:scaffold';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Make Scaffold. Woo!';


	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;


	/**
	 * The Composer instance.
	 *
	 * @var \Illuminate\Support\Composer
	 */
	protected $composer;






	/**
	 * Create a new command instance.
	 *
	 * @param \Illuminate\Filesystem\Filesystem $files
	 * @param  \Illuminate\Support\Composer  $composer
	 *
	 */
	public function __construct(Filesystem $files, Composer $composer)
	{

		parent::__construct();


		$this->files = $files;
		$this->composer = $composer;



	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{

		// Model already exists, return


		// standarize name format - sutdlycase singular
		$name = studly_case(str_singular(trim($this->argument('name'))));


		// parse schema string into collection
		$schemaCollection = $this->parseSchema($this->option('schema'));



		$this->table(['name','type'], $schemaCollection);




		$sg = new ScaffoldGenerator($this->files, $this);

		$sg->generate($name, $schemaCollection);

		//$mg = new MigrationGenerator($this->files, $this);
		//$mg->generate($this->name, $this->schema);
		//$bob = $mg->getMigrationPath();



		// dump-autoload for the entire framework to make sure
		// that the migrations are registered by the class loaders.
		//$this->composer->dumpAutoloads();

		$this->info('Make Scaffold Command Finished.');

		//  Route::resource('product-details', 'ProductDetailController');
		$this->info('Make Scaffold Command Finished.');
	}


	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['name', InputArgument::REQUIRED, 'The name of the class'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['schema', 's', InputOption::VALUE_OPTIONAL, 'Optional schema to be attached to the migration', null],
		];
	}


	/**
	 * Parse the Schema string input from the command line
	 *
	 * @param $schemaString
	 * @return \Illuminate\Support\Collection
	 */
	private function parseSchema($schemaString){

		$schemaArray = explode(',', $schemaString);

		$schemaCollection = collect([]);
		foreach ($schemaArray as $item) {
			$itemArray = explode(":", $item);
			$schemaCollection->push(['name' => $itemArray[0],'type' => $itemArray[1]]);
		}

		return $schemaCollection;

	}




}
