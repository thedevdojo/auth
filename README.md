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

<a href="https://devdojo.com/auth" target="_blank"><img src="https://cdn.devdojo.com/images/june2024/devdojo-auth-image.png" class="w-full h-full" style="border-radius:10px" /></a>

Be sure to visit the official documentation at <a href="https://devdojo.com/auth/docs" target="_blank">https://devdojo.com/auth/docs</a>

## Installation

To install this package you'll want to first have Laravel Breeze, Jetstream, Genesis, or any other Laravel starter kit installed. Then you'll need to install the package:

```
composer require devdojo/auth
```

After the package has been installed you'll need to publish the authentication assets, configs, and more:

```
php artisan vendor:publish --tag=auth:assets
php artisan vendor:publish --tag=auth:config
php artisan vendor:publish --tag=auth:ci
php artisan vendor:publish --tag=auth:migrations
```

Next, run the migrations:

```php
php artisan migrate
```

Finally extend the `DevDojo\User

```
class User extends Devdojo\Auth\Models\User
```

in your `App\Models\User` model. 

Now, you're ready to rock! Auth has just been isntalled and you'll be able to visit the following authentication routes:

 - Login (project.test/auth/login)
 - Register (project.test/auth/register)
 - Forgot Password (project.test/auth/register)
 - Password Reset (project.test/auth/password/reset)
 - Password Reset Token (project.test/auth/password/ReAlLyLoNgPaSsWoRdReSeTtOkEn)
 - Password Confirmation (project.test/auth/password/confirm)

## (Optional) Adding the HasSocialProviders Trait.

You can add all the social auth helpers to your user model by including the following Trait:

```php
<?php

namespace App\Models;

use Devdojo\Auth\Traits\HasSocialProviders; // Import the trait

class User extends Devdojo\Auth\Models\User
{
    use HasSocialProviders; // Use the trait in the User model

    // Existing User model code...
}
```

## License

The DevDojo Auth package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
