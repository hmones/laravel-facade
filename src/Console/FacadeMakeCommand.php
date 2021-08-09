<?php

namespace Hmones\LaravelFacade\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FacadeMakeCommand extends GeneratorCommand
{
    protected $stubType = 'class';

    protected $facadeName;
    protected $implementedClass;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:facade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new facade class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Facade';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->facadeName = $this->getNameInput();
        $this->implementedClass = $this->getClassInput();

        if ($this->isReservedName($this->facadeName)) {
            $this->error("The name '$this->facadeName' is reserved by PHP.");

            return;
        }

        if (! class_exists($this->implementedClass)) {
            $this->error("The class '$this->implementedClass' does not exist, please create it first.");

            return;
        }

        if ($this->createFacade($this->facadeName)) {
            $this->configureFacade();
            $this->info($this->type.' created successfully.');
        }
    }

    /**
     * Get the desired class to be implemented from the input.
     *
     * @return string
     */
    protected function getClassInput(): string
    {
        return trim($this->argument('class namespace'));
    }

    /**
     * Create the Facade file if it doesn't exist.
     *
     * @param string $name
     * @return bool
     * @throws FileNotFoundException
     */
    protected function createFacade(string $name): bool
    {
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $nameSpace = $this->qualifyClass($name);
        $path = $this->getPath($nameSpace);

        $this->makeDirectory($path);
        $this->stubType = 'class';
        $this->files->put($path, $this->generateStub($name, $nameSpace));

        return true;
    }

    /**
     * Build the class with the given name.
     *
     * @param string $className
     * @param string $nameSpace
     * @return string
     * @throws FileNotFoundException
     */
    protected function generateStub(string $className, string $nameSpace): string
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceNamespace($stub, $nameSpace);

        $stub = $this->stubType === 'class'
            ? $this->replaceClass($stub, $className)
            : $this->replaceImplementedClass($stub, $nameSpace);

        return $this->replaceFacadeName($stub, $className);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return $this->stubType === 'class' ? __DIR__.'/stubs/facade.stub' : __DIR__.'/stubs/binding.stub';
    }

    /**
     * Replace the namespace of the implemented class for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceImplementedClass(string $stub, string $name): string
    {
        return str_replace(
            '{{ implementedClass }}',
            $name,
            $stub
        );
    }

    /**
     * Replace the facade name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceFacadeName(string $stub, string $name): string
    {
        return str_replace(
            '{{ facadeName }}',
            $name,
            $stub
        );
    }

    /**
     * Configure Laravel Facade.
     *
     * @return void
     * @throws FileNotFoundException
     */
    protected function configureFacade(): void
    {
        $this->comment('Publishing Facade Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'laravel-facade-provider']);
        $this->updateAppConfig();
        $this->comment('Updating Facade Service Provider...');
        $this->updateServiceProvider();
    }

    /**
     * Update the app configuration file to include the service provider.
     *
     * @return void
     * @throws FileNotFoundException
     */
    protected function updateAppConfig(): void
    {
        $className = config('laravel-facade.provider.name').'::class';
        $class = config('laravel-facade.provider.namespace').'\\'.$className;

        $appConfig = $this->files->get(config_path('app.php'));

        if (preg_match("/$className/", $appConfig)) {
            return;
        }

        $pattern = '/(\'providers\'\s*?=>\s*?\[[^]]*)(class?,)(\s*],)/';
        $appConfig = preg_replace($pattern, '$1'."class,\n\t\t$class,\n".'$3', $appConfig);
        $this->files->put(config_path('app.php'), $appConfig);
    }

    /**
     * Update service provider by instantiating the implementation class.
     *
     * @return void
     * @throws FileNotFoundException
     */
    protected function updateServiceProvider(): void
    {
        $serviceProvider = $this->files->get(app_path($this->getProviderPath()));

        if (preg_match("/$this->implementedClass/", $serviceProvider)) {
            return;
        }

        $this->stubType = 'binding';
        $replacement = $this->generateStub($this->facadeName, $this->implementedClass);
        $pattern = '/(boot\s*\([^\)]*\)[:\w\s]*)(?<body>(\{(?:[^{}]+|(?&body))*)\})/';
        $serviceProvider = preg_replace($pattern, '$1'.'$3'.$replacement."\t}", $serviceProvider);
        $this->files->put(app_path($this->getProviderPath()), $serviceProvider);
    }

    /**
     * Get the service provider path.
     *
     * @return string
     */
    protected function getProviderPath(): string
    {
        $name = config('laravel-facade.provider.namespace');

        return str_replace($this->getNamespace($name).'\\', '', $name).'/'.config('laravel-facade.provider.name').'.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Facades';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the facade already exists'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['class namespace', InputArgument::REQUIRED, 'The namespace of the class the facade will implement'],
        ];
    }

    /**
     * Get the service provider path.
     *
     * @return string
     */
    protected function getProviderPath(): string
    {
        $name = config('laravel-facade.provider.namespace');

        return str_replace($this->getNamespace($name).'\\', '', $name).'/'.config('laravel-facade.provider.name').'.php';
    }
}
