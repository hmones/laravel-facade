<h1 align="center">Laravel Facade</h1>

<p align="center">
<a href="https://github.com/hmones/laravel-facade/actions"><img src="https://github.com/hmones/laravel-facade/actions/workflows/build.yml/badge.svg" alt="Build Status"></a>
<a href="https://github.styleci.io/repos/390311402"><img src="https://github.styleci.io/repos/390311402/shield" alt="Style CI"></a>
<a href="https://packagist.org/packages/hmones/laravel-facade"><img src="https://img.shields.io/packagist/dt/hmones/laravel-facade" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/hmones/laravel-facade"><img src="https://img.shields.io/packagist/v/hmones/laravel-facade" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/hmones/laravel-facade"><img src="https://img.shields.io/packagist/l/hmones/laravel-facade" alt="License"></a>
</p>

This package makes the process of creating facades in laravel super easy and with one simple artisan command. For each
facade created with this package:

- A Facade file is created in ```App\Facades``` to define the facade accessor.
- A Facade service provider ```App\Providers\FacadeServiceProvider.php``` is created/edited to bind your implementation
  class to the facade accessor.
- The ```FacadeServiceProvider``` is registered in your app configuration file (```config\app.php```).

## Installation

Via Composer

```bash
composer require hmones/laravel-facade --dev
```

## Configuration

To publish the package configuration

```bash
php artisan vendor:publish --tag=laravel-facade-config
 ```

The configuration contains the following values:

```php
<?php

return [
    'provider' => [
        'name'      => 'FacadeServiceProvider',
        'namespace' => 'App\Providers',
    ],
];
```

- The **name** attribute represents the name of the provider file that will hold all the bindings between Facades and
  implementation classes, this provider will be then registered automatically in the app configuration.
- The **namespace**
  attribute represents the namespace for your application providers and where the FacadeServiceProvider file will be
  created, so in case you use a different folder you need to change that.

## Usage

To create a new Facade for a particular class you can simply use the following command which accepts two inputs:

- **Facade Name**: The name of the Facade class that you would like to create.
- **Class Path**: the path to the class file that you would like to implement inside the app directory of your
  application. e.g. ExampleController can be located by default at ```Http\Controllers\ExampleController.php```

```bash
php artisan make:facade FacadeName Custom/ImplementedClass.php
 ```

To remove a Facade

```bash
php artisan remove:facade FacadeName
 ```

To publish the facade service provider
**Note**: You normally do not need to do this step because if the provider doesn't exist, it will be automatically
published when you create a new Facade.

```bash
php artisan vendor:publish --tag=laravel-facade-provider
 ```

## Change log

Please see the [changelog](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [contributing.md](CONTRIBUTING.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Maab Javid][link-author2]
- [Haytham Mones][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](LICENSE.md) for more information.



[link-author]: https://github.com/hmones

[link-author2]: https://github.com/mabjavaid

[link-contributors]: ../../contributors
