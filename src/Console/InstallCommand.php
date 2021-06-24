<?php


namespace StrongNguyen29\ApiService\Console;


use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'apiservice:install';

    protected $description = 'Install package api-service';

    public function handle() {
        $this->call('vendor:publish --tag=service');
    }
}