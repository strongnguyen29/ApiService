<?php
namespace StrongNguyen29\ApiService\Console;

use Illuminate\Console\GeneratorCommand;

class MakeServiceInterfaceCommand extends GeneratorCommand
{
    protected $name = 'make:api-service-interface';

    protected $description = 'Create new service interface';

    protected $type = 'Service';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/interface.php.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\ApiServices\Contracts';
    }

    /**
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if (parent::handle() === false) {
            $this->error('Create Interface failed');
            return false;
        }
        $this->info('Create Interface OK');
    }
}