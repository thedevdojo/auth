<p align="center"><a href="https://devdojo.com" target="_blank"><img src="https://cdn.devdojo.com/images/april2024/dd-auth-logo.png" width="auto" height="64px" alt="Auth Logo"></a></p>
<br>
<p align="center">
<a href="https://github.com/thedevdojo/auth/actions"><img src="https://github.com/thedevdojo/auth/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/dt/devdojo/auth" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/v/devdojo/auth" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/l/devdojo/auth" alt="License"></a>
</p>

## About

Auth is a plug'n play authentication package for any <a href="https://laravel.com" target="_blank">Laravel application</a>.

<a href="https://devdojo.com/auth" target="_blank"><img src="https://cdn.devdojo.com/images/june2024/pages.jpeg" class="w-full h-full" style="border-radius:10px" /></a>

Be sure to visit the official documentation at [https://devdojo.com/auth/docs](https://devdojo.com/auth/docs)

## Installation

To install this package you'll want to first have Laravel Breeze, Jetstream, Genesis, or any other Laravel starter kit installed. Then you'll need to install the package:

```
composer require devdojo/auth
```

After the package has been installed you'll need to publish the authentication assets with the following command:

```
php artisan vendor:publish --tag=auth:assets
```

Auth has just been isntalled and you'll be able to visit the following authentication routes:

 - Login (project.test/auth/login)
 - Register (project.test/auth/register)
 - Forgot Password (project.test/auth/register)
 - Password Reset (project.test/auth/password/reset)
 - Password Reset Token (project.test/auth/password/ReAlLyLoNgPaSsWoRdReSeTtOkEn)
 - Password Confirmation (project.test/auth/password/confirm)

## Auth Migrations

You'll also want to include the auth migrations:

```
php artisan migrate --path=vendor/devdojo/auth/database/migrations 
```

This will add a new `social_provider_user` table and it will also allow the `name` and `password` fields in the default `user` table to be nullable.

## Auth Config

You will also need to publish the auth config by running the following:

```
php artisan vendor:publish --tag=auth:config
```

## Adding the HasSocialProviders Trait.

You can add all the social auth helpers to your user model by including the following Trait:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Devdojo\Auth\Traits\HasSocialProviders; // Import the trait

class User extends Authenticatable
{
    use HasSocialProviders; // Use the trait in the User model

    // Existing User model code...
}
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
