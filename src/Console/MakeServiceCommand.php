<?php
namespace StrongNguyen29\ApiService\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeServiceCommand extends GeneratorCommand
{
    protected $name = 'make:api-service';

    protected $description = 'Create new service';

    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/service.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\ApiServices';
    }

    /**
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (parent::handle() === false) {
            $this->error('Create Failed');
            return false;
        }

        $this->replaceContentService();

        $this->createConfigFile();

        if ($this->hasOption('interface')) {
            $this->createInterface();
        } elseif ($this->hasOption('facade')) {
            $this->createFacade();
        } else {
            $this->createFacade();
        }
    }

    /**
     *
     */
    protected function replaceContentService() {
        // Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        // get the destination path, based on the default namespace
        $path = $this->getPath($class);

        $content = file_get_contents($path);

        // Update name binding
        $content = str_replace('{{CONFIG_FILE_NAME}}', $this->getConfigFileName(), $content);

        file_put_contents($path, $content);
        $this->info('Create ' . $class . ' OK');
    }

    /**
     * @return string
     */
    protected function getNameArg() {
        return trim($this->argument('name'));
    }

    /**
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name')) . 'Service';
    }

    /**
     * @return string
     */
    protected function getConfigFileName() {
        return 'api_service_' . Str::snake($this->getNameArg());
    }

    /**
     * Create api endpoint config file
     */
    protected function createConfigFile() {
        $path = base_path('config/' . $this->getConfigFileName()) . '.php';

        $content = $this->files->get(__DIR__ . '/stubs/config.php.stub');

        $content = str_replace('{{CONFIG_NAME}}', Str::upper(Str::snake($this->getNameArg())), $content);

        $this->makeDirectory($path);
        $this->files->put($path, $content);
        $this->info('create config file ok');
    }

    /**
     * Create interface
     */
    protected function createInterface() {
        $name = Str::studly($this->getNameArg()) . 'Interface';
        $this->call('make:api-service-interface', array_filter(['name' => $name]));
        $this->registerInProvider($this->getInterfaceProviderRegister($name));
    }

    /**
     * Create facade
     */
    protected function createFacade() {
        $name = Str::studly($this->getNameArg()) . 'Facade';
        $this->call('make:api-service-facade', array_filter(['name' => $name]));
        $this->registerInProvider($this->getFacadeProviderRegister($name));
    }

    /**
     * @param $replace
     */
    protected function registerInProvider($replace) {
        try {
            $searchStr = '        /** REGISTER_API_SERVICE **/';

            $path = base_path('app/Providers/ApiServiceProvider.php');

            $content = $this->files->get($path);

            $content = str_replace($searchStr, '        ' . $replace . PHP_EOL . $searchStr, $content);

            $this->files->put($path, $content);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * @param $name
     * @return string
     */
    protected function getFacadeProviderRegister($name) {
        return sprintf(
            '$this->app->singleton(\'%s\', function ($app) { 
            return new \%s; 
        });' . PHP_EOL, $name, $this->qualifyClass($this->getNameInput()) . '()'
        );
    }

    /**
     * @param $name
     * @return string
     */
    protected function getInterfaceProviderRegister($name) {
        return sprintf('$this->app->singleton(\%s, \%s);',
            $this->qualifyClass('Contracts\\' . $name) . '::class',
            $this->qualifyClass($this->getNameInput()) . '::class'
        );
    }
    
    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['interface', 'i', InputOption::VALUE_OPTIONAL, 'The binding with interface'],
        ];
    }
}
