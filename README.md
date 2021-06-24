## Base api service call for laravel or lumen

Thư viện base api cho laravel hoặc lumen.

***
#### Cài đặt

1. Composer install

    `composer require strongnguyen29/apiservice`
    
2. publish service provider
    
    `php artisan apiservice:install`
    
    Sẽ copy ApiServiceProvider.php vào `app/Providers`
    
3. Đăng ký service provider

    - Laravel: Đăng ký vào file config/app.php 'providers'
        
        `App\Providers\ApiServiceProvider::class`
        
    - Lumen: Đăng ký vào file: bootstrap/app.php
    
        `$app->register(App\Providers\ApiServiceProvider::class);`

***
#### Tạo API Service
- **Api với facade**

    `php artisan make:api-service ProductCatalog`
   
   Sẽ tạo 3 file: 
   
    * `config/api_service_product_catalog.php` 
    * `app/ApiServices/ProductCatalogService.php` 
    * `app/ApiServices/Facades/ProductCatalogFacade.php`
    
    Laravel: đăng ký Facade alias trong file `config/app.php` aliases
        
        'ProductCatalog' => \App\ApiServices\Facades\ProductCatalogFacade::class
        
    Lumen: đăng ký Facade alias trong file `bootstrap/app.php`
    
        $app->withFacades(true, [\App\ApiServices\Facades\ProductCatalogFacade::class => 'ProductCatalog']);
        
- **Api với Interface**

    `php artisan make:api-service ProductCatalog --interface`
   
   Sẽ tạo 3 file: 
   
    * `config/api_service_product_catalog.php` 
    * `app/ApiServices/ProductCatalogService.php` 
    * `app/ApiServices/Contracts/ProductCatalogInterface.php`
    
    