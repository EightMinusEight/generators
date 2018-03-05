<?php

namespace EightMinusEight\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;


class ControllerGenerator {


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

	protected $stubName = "controller.model.stub";


	/**
	 * ControllerGenerator constructor.
	 *
	 * @param Filesystem $files
	 * @param Command $command
	 */
	public function __construct(Filesystem $files, Command $command) {


		$this->command = $command;
		$this->files = $files;
	}


	/**
	 * Generate a new Controller File
	 *
	 * @param string $name
	 * @param Collection $schema
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function generate(string $name, Collection $schema){

		$this->name = $name;
		$this->schema = $schema;


		$outputFile = $this->getControllerPath() . '/' . $this->getControllerFileName($name);

		// get the stub
		$stub = $this->files->get(__DIR__ . '/stubs/' . $this->stubName);

		// parse and populate the sub
		$newStub = $this->populateStub($name, $stub, $schema);


		// make sure directory exists, if not, make it
		$this->makeDirectory($outputFile);

		// write file
		$this->files->put($outputFile, $newStub);

		// send a message
		$this->command->info('Controller Created: ' . $this->getControllerFileName($name));

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

		$className = studly_case($name) . 'Controller';

		$stub = str_replace('DummyClass', $className, $stub);


		$namespace = 'App\Http\Controllers';

		$stub = str_replace('DummyNamespace', $namespace, $stub);


		$rootNamespace = 'App\\';

		$stub = str_replace('DummyRootNamespace', $rootNamespace, $stub);



		$fullModelClass = 'App\Models\\' . $name;

		$stub = str_replace('DummyFullModelClass', $fullModelClass, $stub);



		$modelClass = $name;

		$stub = str_replace('DummyModelClass', $modelClass, $stub);



		$modelVariablePlural = snake_case(str_plural($name));

		$stub = str_replace('DummyModelVariablePlural', $modelVariablePlural, $stub);



		$modelVariable = snake_case($name);

		$stub = str_replace('DummyModelVariable', $modelVariable, $stub);



		$viewName = kebab_case(str_plural($name));

		$stub = str_replace('DummyViewName', $viewName, $stub);




		$fieldsString = "";
		foreach ($schema as $item) {
			$fieldsString .= $this->getField($item['name'],$item['type']);
		}

		$stub = str_replace('DummyFields', $fieldsString, $stub);



		return $stub;

	}


	/**
	 * Get the Generated field text
	 *
	 * @param $name
	 * @param $type
	 * @return string
	 */
	protected function getField($name, $type) {

		$field = "'" . $name . "'\t\t => 'required',\r\n\t\t\t";

		return $field;
	}


	/**
	 * Get the path to the models directory.
	 *
	 * @return string
	 */
	protected function getControllerPath()
	{
		return app_path().DIRECTORY_SEPARATOR.'Http'.DIRECTORY_SEPARATOR.'Controllers';
	}


	/**
	 * Get the filename of the new migration file
	 *
	 * @param $name
	 * @return string
	 */
	protected function getControllerFileName($name)
	{
		return studly_case($name) . 'Controller.php';
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