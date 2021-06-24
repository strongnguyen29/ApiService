<?php
namespace StrongNguyen29\ApiService\Console;

use Illuminate\Console\GeneratorCommand;

class MakeServiceFacadeCommand extends GeneratorCommand
{
    protected $name = 'make:api-service-facade';

    protected $description = 'Create new service facade';

    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/facade.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\ApiServices\Facades';
    }

    /**
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (parent::handle() === false) {
            $this->error('Create Facade failed');
            return false;
        }

        // Get the fully qualified class name (FQN)
        $class = $this->qualifyClass($this->getNameInput());

        // get the destination path, based on the default namespace
        $path = $this->getPath($class);

        $content = file_get_contents($path);

        // Update name binding
        $content = str_replace('{{SERVICE_NAME}}', $this->getNameInput(), $content);

        file_put_contents($path, $content);
        $this->info('Create Facade OK');
    }
}