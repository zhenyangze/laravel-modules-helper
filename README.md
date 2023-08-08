# laravel modules helper
this is a package for creating restful api with `nwidart/laravel-modules`, the first version is aim to ***create restful api*** in modules.

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
| command    | description                          | args    |
|:-----------|:-------------------------------------|:--------|
| module:api | Create a new module Controller class | --model |


## Usage
1. create modules, such as Common
```php
php artisan make:module Common
```

2. create restfull api

before you create the api, you shoule make sure the model is exists.
```php
php artisan module:api common Api/CommonNewsController --model App\\Models\\CommonNews
```

3. add route to the `api.php`

in my website is app/Modules/Common/Routes/api.php,you should choose the right module.
```php
Route::resource('common/news', 'CommonNewsController');
```

4. test the restful api

| method | url               | comment |
|:-------|:------------------|---------|
| get    | api/common/news   |         |
| post   | api/common/news   | form    |
| get    | api/common/news/1 |         |
| put    | api/common/news/1 | raw     |
| delete | api/common/news/1 |         |
