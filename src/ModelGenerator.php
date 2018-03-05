<?php

namespace EightMinusEight\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;


class ModelGenerator {


	/**
	 * The class name
	 *
	 * @var string
	 */
	protected $name;    // "Test"

	/**
	 * The schema field array
	 *
	 * @var Collection
	 */
	protected $schema;    // [ ['name' => 'title', 'type' => 'string'] ]

	/**
	 * The filesystem instance.
	 *
	 * @var Filesystem
	 */
	protected $files;

	/**
	 * The command instance.
	 *
	 * @var Command
	 */
	protected $command;

	protected $stubName = "model.stub";


	/**
	 * ModelGenerator constructor.
	 *
	 * @param Filesystem $files
	 * @param Command $command
	 */
	public function __construct(Filesystem $files, Command $command) {


		$this->command = $command;
		$this->files = $files;
	}


	/**
	 * Generate a new Model File
	 *
	 * @param string $name
	 * @param Collection $schema
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function generate(string $name, Collection $schema){

		$this->name = $name;
		$this->schema = $schema;


		$outputFile = $this->getModelPath() . '/' . $this->getModelFileName($name);

		// get the stub
		$stub = $this->files->get(__DIR__ . '/stubs/' . $this->stubName);

		// parse and populate the sub
		$newStub = $this->populateStub($name, $stub, $schema);


		// make sure directory exists, if not, make it
		$this->makeDirectory($outputFile);

		// write file
		$this->files->put($outputFile, $newStub);

		// send a message
		$this->command->info('Model Created: ' . $this->getModelFileName($name));

	}


	/**
	 * Populate the stub file with values
	 *
	 * @param $name
	 * @param $stub
	 * @param $schema
	 * @return mixed
	 */
	protected function populateStub($name, $stub, $schema) {

		$className = studly_case($name);

		$stub = str_replace('DummyClass', $className, $stub);


		$namespace = 'App\Models';

		$stub = str_replace('DummyNamespace', $namespace, $stub);



		return $stub;

	}





	/**
	 * Get the path to the models directory.
	 *
	 * @return string
	 */
	protected function getModelPath()
	{
		return app_path().DIRECTORY_SEPARATOR.'Models';
	}


	/**
	 * Get the filename of the new migration file
	 *
	 * @param $name
	 * @return string
	 */
	protected function getModelFileName($name)
	{
		return studly_case($name) . '.php';
	}


	/**
	 * Build the directory for the class if necessary.
	 *
	 * @param  string  $path
	 * @return string
	 */
	protected function makeDirectory($path)
	{
		if (! $this->files->isDirectory(dirname($path))) {
			$this->files->makeDirectory(dirname($path), 0777, true, true);
		}

		return $path;
	}

}