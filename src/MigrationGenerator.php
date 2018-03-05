<?php

namespace EightMinusEight\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;


class MigrationGenerator {


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

	protected $stubName = "migration.stub";


	/**
	 * MigrationGenerator constructor.
	 *
	 * @param Filesystem $files
	 * @param Command $command
	 */
	public function __construct(Filesystem $files, Command $command) {


		$this->command = $command;
		$this->files = $files;
	}


	/**
	 * Generate a new Migration File
	 *
	 * @param string $name
	 * @param Collection $schema
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function generate(string $name, Collection $schema){

		$this->name = $name;
		$this->schema = $schema;


		$outputFile = $this->getMigrationPath() . '/' . $this->getMigrationFileName($name);

		$stub = $this->files->get(__DIR__ . '/stubs/' . $this->stubName);

		$newStub = $this->populateStub($name, $stub, $schema);

		// write file
		$this->files->put($outputFile, $newStub);

		$this->command->info('Migration Created: ' . $this->getMigrationFileName($name));

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

		$className = $this->getClassName($name);
		$tableName = str_plural(snake_case(trim($name)));

		$stub = str_replace('DummyClass', $className, $stub);

		$stub = str_replace('DummyTable', $tableName, $stub);

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

		$field = '$table->' . $type . "('" . $name . "');\r\n\t\t\t";

		return $field;
	}



	/**
	 * Get the path to the migration directory.
	 *
	 * @return string
	 */
	protected function getMigrationPath()
	{
		return database_path().DIRECTORY_SEPARATOR.'migrations';
	}


	/**
	 * Get the filename of the new migration file
	 *
	 * @param $name
	 * @return string
	 */
	protected function getMigrationFileName($name){

		return date('Y_m_d_His') . '_create_' . str_plural(snake_case(trim($name))) . '_table.php';

	}

	/**
	 * Get the class name of a migration name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	protected function getClassName($name)
	{
		return 'Create' . str_plural(studly_case($name)) . 'Table';
	}



}