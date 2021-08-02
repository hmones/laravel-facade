<?php

namespace Hmones\LaravelFacade\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class FacadeMakeCommand extends GeneratorCommand
{
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
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(): void
    {

        if ($this->isReservedName($this->getNameInput())) {
            $this->error('The name "' . $this->getNameInput() . '" is reserved by PHP.');

            return;
        }

        $facadeName = $this->getNameInput();

        $facadeNameSpace = $this->qualifyClass($facadeName);

        $path = $this->getPath($facadeNameSpace);

        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return;
        }

        $this->makeDirectory($path);

        $this->files->put($path, $this->generateClass($facadeName, $facadeNameSpace));

        $this->publishServiceProvider();

        $this->info($this->type . ' created successfully.');
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     */
    protected function generateClass($name, $nameSpace)
    {
        $stub = $this->files->get($this->getStub());

        $this->replaceNamespace($stub, $nameSpace);

        $stub = $this->replaceClass($stub, $name);

        return $this->replaceFacadeName($stub, $name);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/facade.stub';
    }

    /**
     * Replace the facade name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceFacadeName($stub, $name)
    {
        return str_replace(
            '{{ facadeName }}',
            $name,
            $stub
        );
    }

    protected function publishServiceProvider(): void
    {
        $path = 'Providers\FacadeServiceProvider.php';
        if (!$this->files->exists(app_path($path))) {
            $this->createServiceProvider($path);
            $this->updateAppConfig('App\Providers\FacadeServiceProvider::class');
        }
    }

    /**
     * Create service provider file
     *
     * @param string $path
     * @return void
     */
    protected function createServiceProvider($path): void
    {
        $this->files->put(app_path($path), file_get_contents(__DIR__ . '/../' . $path));
    }

    /**
     * Update the app configuration file to include the service provider
     *
     * @param string $class
     * @return void
     */
    protected function updateAppConfig($class): void
    {
        $appConfig = file_get_contents(config_path('app.php'));

        if (preg_match($class, $appConfig)) {

            return;
        }

        $pattern = '/(\'providers\'\s.*?=>\s.*?\[[^\]]*)(class\,?\n?.*)(\],)/';
        $replacement = 'class,' . PHP_EOL . '        ' . $class . ',' . PHP_EOL;
        $appConfig = preg_replace($pattern, '$1' . $replacement . '$3', $appConfig);
        $this->files->put(config_path('app'), $appConfig);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Facades';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the facade already exists']
        ];
    }
}
