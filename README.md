## Base api service call for laravel or lumen

### install

1. install composer

    `composer require strongnguyen29/apiservice`
    
2. publish service provider
    
    `php artisan vendor:publish --tag=service`
    
3. create service

- create with Facade
    
    `php artisan make:api-service Foo --facade`
    
- create with interface
        
    `php artisan make:api-service Foo --interface`
    