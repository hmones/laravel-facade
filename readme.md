# Laravel Facade

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-packagist]
[![Build Status][ico-github]][link-github]
[![StyleCI][ico-styleci]][link-styleci]
[![License][ico-license]][link-packagist]

This package makes the process of creating facades in laravel super easy and with one simple artisan command.
For each facade created with this package:
- A Facade file is created in ```App\Facades``` to define the facade accessor.
- A Facade service provider ```App\Providers\FacadeServiceProvider.php``` is created/edited to bind your implementation class to the facade accessor.
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

To create a new Facade for a particular class you can simply use the following command

```bash
php artisan make:facade FacadeName "App\Custom\ImplementatedClass"
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

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [Maab Javid][link-author2]
- [Haytham Mones][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/hmones/laravel-facade.svg

[ico-downloads]: https://img.shields.io/packagist/dt/hmones/laravel-facade.svg

[ico-github]: https://github.com/hmones/laravel-facade/actions/workflows/build.yml/badge.svg

[ico-styleci]: https://github.styleci.io/repos/390311402/shield

[ico-license]: https://img.shields.io/packagist/l/hmones/laravel-facade.svg

[link-packagist]: https://packagist.org/packages/hmones/laravel-facade

[link-github]: https://github.com/hmones/laravel-facade/actions

[link-styleci]: https://github.styleci.io/repos/390311402

[link-author]: https://github.com/hmones

[link-author2]: https://github.com/mabjavaid

[link-contributors]: ../../contributors
