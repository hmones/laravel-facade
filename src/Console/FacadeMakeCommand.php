<?php

namespace Hmones\LaravelFacade\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FacadeMakeCommand extends GeneratorCommand
{
    protected const SERVICE_PROVIDER_PATH = 'Providers/FacadeServiceProvider.php';
    protected const SERVICE_PROVIDER_CLASS = 'App\Providers\FacadeServiceProvider::class';
    protected const SERVICE_PROVIDER_CLASS_NAME = 'FacadeServiceProvider::class';

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
        $facadeName = $this->getNameInput();
        $implementedClass = $this->getClassInput();

        if ($this->isReservedName($facadeName) || $this->isReservedName($implementedClass)) {
            $this->error("The name '{$facadeName}' or '{$implementedClass}' is reserved by PHP.");

            return;
        }

        if (!class_exists($implementedClass)) {
            $this->error("The class '{$implementedClass}' does not exist, please create it first.");

            return;
        }

        if ($this->createFacade($facadeName)) {
            $this->configureFacade();
            $this->info($this->type . ' created successfully.');
        }
    }

    /**
     * Get the desired class to be implemented from the input.
     *
     * @return string
     */
    protected function getClassInput()
    {
        return trim($this->argument('class namespace'));
    }

    /**
     * Create the Facade file if it doesn't exist
     *
     * @param $name
     * @return void
     */
    protected function createFacade(string $name): bool
    {
        if ((!$this->hasOption('force') ||
                !$this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type . ' already exists!');

            return false;
        }

        $nameSpace = $this->qualifyClass($name);
        $path = $this->getPath($nameSpace);

        $this->makeDirectory($path);

        $this->files->put($path, $this->generateClass($name, $nameSpace));

        return true;
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

    protected function configureFacade(): void
    {
        $this->createServiceProvider();
        $this->updateAppConfig();
        $this->updateServiceProvider();
    }

    /**
     * Create service provider file
     *
     * @return void
     */
    protected function createServiceProvider(): void
    {
        if ($this->files->exists(app_path(self::SERVICE_PROVIDER_PATH))) {

            return;
        }

        $this->files->copy(__DIR__ . '/../' . self::SERVICE_PROVIDER_PATH, app_path(self::SERVICE_PROVIDER_PATH));
    }

    /**
     * Update the app configuration file to include the service provider
     *
     * @return void
     */
    protected function updateAppConfig(): void
    {
        $appConfig = file_get_contents(config_path('app.php'));

        if (preg_match('/' . self::SERVICE_PROVIDER_CLASS_NAME . '/', $appConfig)) {

            return;
        }

        $pattern = '/(\'providers\'\s*?=>\s*?\[[^\]]*)(class[,]?)(\s*\],)/';
        $replacement = "class,\n\t\t" . self::SERVICE_PROVIDER_CLASS . ",\n";
        $appConfig = preg_replace($pattern, '$1' . $replacement . '$3', $appConfig);

        if (!preg_match('/' . self::SERVICE_PROVIDER_CLASS_NAME . '/', $appConfig)) {
            $this->warn('Failed to register service provider in app.config, please add it manually.');

            return;
        }

        $this->files->put(config_path('app.php'), $appConfig);
        $this->info('The FacadeServiceProvider has been successfully added to your app configuration');
    }

    /**
     * Update service provider by instantiating the implementation class
     *
     * @return void
     */
    protected function updateServiceProvider(): void
    {
        $serviceProvider = file_get_contents(app_path(self::SERVICE_PROVIDER_PATH));
        $implementedClass = $this->getClassInput();
        $facadeName = $this->getNameInput();

        if (preg_match("/{$implementedClass}/", $serviceProvider)) {

            return;
        }

        $replacement = "\t\$this->app->bind('{$facadeName}',function(){return resolve('{$implementedClass}');});\n\t}";
        $pattern = '/(boot\s*\([^\)]*\)\s*:.*\s*)(?<body>\{(?:[^{}]+|(?&body))*(\}))/';
        preg_match($pattern, $serviceProvider, $bootMethod);
        $bootMethod = substr($bootMethod[0], 0, -1);
        $replacement = $bootMethod . $replacement;
        $serviceProvider = preg_replace($pattern, $replacement, $serviceProvider);
        $this->files->put(app_path(self::SERVICE_PROVIDER_PATH), $serviceProvider);
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

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the class'],
            ['class namespace', InputArgument::REQUIRED, 'The namespace of the class the facade will implement'],
        ];
    }
}
