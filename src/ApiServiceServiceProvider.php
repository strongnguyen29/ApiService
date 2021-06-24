<?php

namespace StrongNguyen29\ApiService;

use Illuminate\Support\ServiceProvider;

class ApiServiceServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register() {

    }

    /**
     * {@inheritdoc}
     */
    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \StrongNguyen29\ApiService\Console\InstallCommand::class,
                \StrongNguyen29\ApiService\Console\MakeServiceCommand::class,
                \StrongNguyen29\ApiService\Console\MakeServiceInterfaceCommand::class,
                \StrongNguyen29\ApiService\Console\MakeServiceFacadeCommand::class
            ]);
        }

        $this->publishes([
            __DIR__. '/../Providers/ApiServiceProvider.php.stub' => base_path('app/Providers/ApiServiceProvider.php'),
        ], 'service');
    }
}