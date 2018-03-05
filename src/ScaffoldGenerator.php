<?php

namespace EightMinusEight\Generators;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class ScaffoldGenerator {


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


	/**
	 * ScaffoldGenerator constructor.
	 *
	 * @param Filesystem $files
	 * @param \Illuminate\Console\Command $command
	 */
	public function __construct(Filesystem $files, Command $command) {

		$this->command = $command;
		$this->files = $files;
	}


	/**
	 * Generate the files
	 *
	 * @param string $name
	 * @param Collection $schema
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function generate(string $name, Collection $schema){

		$this->name = $name;
		$this->schema = $schema;

		$this->makeMigration();
	    $this->makeModel();
	    $this->makeController();
	    $this->makeViews();
	}


	/**
	 * Make the migration file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeMigration() {
		$mg = new MigrationGenerator($this->files, $this->command);
		$mg->generate($this->name, $this->schema);
	}


	/**
	 * Make the Model file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeModel() {
		$mg = new ModelGenerator($this->files, $this->command);
		$mg->generate($this->name, $this->schema);
	}


	/**
	 * Make the Controller file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeController() {
		$cg = new ControllerGenerator($this->files, $this->command);
		$cg->generate($this->name, $this->schema);
	}


	/**
	 * Make the View Files
	 */
	protected function makeViews() {
		$this->makeIndexView();
		$this->makeShowView();
		$this->makeCreateView();
		$this->makeEditView();
	}

	/**
	 * Make the index view file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeIndexView() {
		$vg = new ViewIndexGenerator($this->files, $this->command);
		$vg->generate($this->name, $this->schema);
	}

	/**
	 * Make the show view file
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeShowView() {
		$vg = new ViewShowGenerator($this->files, $this->command);
		$vg->generate($this->name, $this->schema);
	}

	/**
	 * Make the create view file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeCreateView() {
		$vg = new ViewCreateGenerator($this->files, $this->command);
		$vg->generate($this->name, $this->schema);
	}

	/**
	 * Make the edit view file
	 *
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function makeEditView() {
		$vg = new ViewEditGenerator($this->files, $this->command);
		$vg->generate($this->name, $this->schema);
	}



}