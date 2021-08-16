<?php

namespace Hmones\LaravelFacade\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class FacadeRemoveCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'remove:facade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a registered facade class';

    /**
     * Remove registered facade.
     *
     * @param  Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->facadeName = trim($this->argument('name'));

        if (! class_exists('App\Facades\\'.$this->facadeName)) {
            $this->error("The class '$this->facadeName' does not exist.");

            return;
        }

        $this->removeFacade($this->facadeName);
        $this->info('Facade '.$this->facadeName.' purged successfully.');
    }

    /**
     * Remove the requested class, file and configuration.
     *
     * @return void
     */
    protected function removeFacade(): void
    {
        $this->files->delete(app_path('Facades/'.$this->facadeName.'.php'));
        $pattern = '/.*\''.$this->facadeName.'\'[^}]*}\);\n*/';
        $serviceProvider = $this->files->get($this->getProviderPath());
        $this->files->put($this->getProviderPath(), preg_replace($pattern, '', $serviceProvider));
    }

    /**
     * Get the service provider path.
     *
     * @return string
     */
    protected function getProviderPath(): string
    {
        $providerNamespace = config('laravel-facade.provider.namespace');

        return app_path(str_replace($this->getNamespace($providerNamespace).'\\', '', $providerNamespace).'/'.config('laravel-facade.provider.name').'.php');
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
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
        ];
    }
}
