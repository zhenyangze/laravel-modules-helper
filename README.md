# laravel modules helper
this is a package for creating restful api with `Caffeinated Modules`, the first version is aim to ***create restful api*** in modules.

## Quick Installation
Begin by installing the package through Composer.
```php
composer require yangze/laravel-modules-helper
```
Once this operation is complete, simply add both the service provider and facade classes to your project's `config/app.php` file:

*Service Provider*
```php
Yangze\ModulesHelper\ModulesHelperServiceProvider::class,
```

*publish tag*
```php
php artisan vendor:publish --tag Yangze\ModulesHelper\ModulesHelperServiceProvider
```
## Command list
| command | description | args |
|:--------|:--------|:--------|
|    make:module:helper:controller    |   Create a new module Controller class     | --model, --request, --resource |
| make:module:helper:request | Create a new module form request class for api | |
| make:module:helper:resource | Create a new module resource class ||

## Usage
1. create modules, such as Common
```php
php artisan make:module Common
```

2. create restfull api
==before you create the api, you shoule make sure the model is exists.==
```php
php artisan make:module:helper:controller common Api/CommonNewsController --model App\\Models\\CommonNews --resource --request
```

3. add route to the `api.php`
in my website is app/Modules/Common/Routes/api.php,you should choose the right module.
```php
Route::resource('common/news', 'Api\CommonNewsController');
```

4. test the restful api

| method | url |
|:--------|:--------|
|     get   |    api/common/news   |
|     post  |    api/common/news   |
|     get   |    api/common/news/1   |
|     patch |    api/common/news/1   |
|    delete |    api/common/news/1   |
