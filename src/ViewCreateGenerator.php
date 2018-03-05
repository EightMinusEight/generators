<?php

namespace EightMinusEight\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;


class ViewCreateGenerator {


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

	protected $stubName = "create.stub";


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


		$outputFile = $this->getViewPath() . '/' . kebab_case(str_plural($name)) . '/' . $this->getViewFileName($name);

		// get the stub
		$stub = $this->files->get(__DIR__ . '/stubs/views/' . $this->stubName);

		// parse and populate the sub
		$newStub = $this->populateStub($name, $stub, $schema);


		// make sure directory exists, if not, make it
		$this->makeDirectory($outputFile);

		// write file
		$this->files->put($outputFile, $newStub);

		// send a message
		$this->command->info('View Created: ' . $this->getViewFileName($name));

	}


	/**
	 * Populate the stub file with values
	 *
	 * @param $name
	 * @param $stub
	 * @param $schema
	 * @return mixed
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function populateStub($name, $stub, $schema) {


		$classNamePlural = title_case(str_plural($name));

		$stub = str_replace('DummyClassPlural', $classNamePlural, $stub);



		$className = title_case($name);

		$stub = str_replace('DummyClass', $className, $stub);


		$viewName = kebab_case(str_plural($name));

		$stub = str_replace('DummyViewName', $viewName, $stub);




		$formFieldsString = "";
		foreach ($schema as $item) {
			$formFieldsString .= $this->getField($item['name'],$item['type']);
		}

		$stub = str_replace('DummyFormFields', $formFieldsString, $stub);



		$modelVariablePlural = snake_case(str_plural($name));

		$stub = str_replace('DummyModelVariablePlural', $modelVariablePlural, $stub);


		$modelVariable = snake_case($name);

		$stub = str_replace('DummyModelVariable', $modelVariable, $stub);


		return $stub;

	}


	/**
	 * Get the Generated field text
	 *
	 * @param $name
	 * @param $type
	 * @return string
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function getField($name, $type) {

		// get the stub
		$fieldStub = $this->files->get(__DIR__ . '/stubs/views/partials/create.field.stub');


		$fieldStub = str_replace('DummyField', $name, $fieldStub);

		$fieldStub = str_replace('DummyLabelField', studly_case($name), $fieldStub);

		return $fieldStub;
	}



	/**
	 * Get the path to the models directory.
	 *
	 * @return string
	 */
	protected function getViewPath()
	{
		return resource_path().DIRECTORY_SEPARATOR.'views';
	}


	/**
	 * Get the filename of the new migration file
	 *
	 * @param $name
	 * @return string
	 */
	protected function getViewFileName($name)
	{
		return 'create.blade.php';
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