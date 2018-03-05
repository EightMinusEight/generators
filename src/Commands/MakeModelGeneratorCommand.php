<?php

namespace EightMinusEight\Generators\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;


class MakeModelGeneratorCommand extends GeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:model2';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent Model class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Model';





    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    // Model already exists, return
    	if (parent::handle() === false && ! $this->option('force')) {
		    return;
	    }

	    // Do more stuff
    }


	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return __DIR__ . '/../stubs/model.stub';
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param string $rootNamespace
	 *
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		$model_namespace =  config('eightminuseight.settings.model.namespace');

		return $rootNamespace . '\\' . $model_namespace;
	}





	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, factory, and resource controller for the model'],

			['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],

			['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],

			['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists.'],

			['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model.'],

			['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model.'],

			['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller.'],
		];
	}




}
