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
            return false;
        }

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
        return parent::getNameInput() . 'Service';
    }

    /**
     * Create api endpoint config file
     */
    protected function createConfigFile() {
        $snakeName = Str::snake($this->getNameArg());

        $path = base_path('config/api_service_' . $snakeName) . '.php';

        $content = $this->files->get(__DIR__ . '/stubs/config.php.stub');

        $content = str_replace('{{CONFIG_NAME}}', Str::upper($snakeName), $content);

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
        });' . PHP_EOL,
            $name,
            $this->qualifyClass('Facades\\' . $name) . '()'
        );
    }

    /**
     * @param $name
     * @return string
     */
    protected function getInterfaceProviderRegister($name) {
        return sprintf('$this->app->singleton(%s,%s);',
            $this->qualifyClass('Contracts\\' . $name) . '::class',
            $this->qualifyClass($this->getNameInput())
        );
    }
}