<p align="center"><a href="https://devdojo.com" target="_blank"><img src="https://cdn.devdojo.com/images/april2024/dd-auth-logo.png" width="auto" height="64px" alt="Auth Logo"></a></p>
<br>
<p align="center">
<a href="https://github.com/thedevdojo/auth/actions"><img src="https://github.com/thedevdojo/auth/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/dt/devdojo/auth" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/v/devdojo/auth" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/devdojo/auth"><img src="https://img.shields.io/packagist/l/devdojo/auth" alt="License"></a>
</p>

## About

Auth is a plug'n play authentication package for any <a href="https://laravel.com" target="_blank">Laravel application</a>. We have closed **issues** for this repo and are recommending that anyone who wants to report an issue or make a suggestion to do so here: [https://devdojo.com/questions](https://devdojo.com/questions). Additionally, we are open to any kind of Pull Request 😉

<a href="https://devdojo.com/auth" target="_blank"><img src="https://cdn.devdojo.com/images/june2024/devdojo-auth-image.png" class="w-full h-full" style="border-radius:10px" /></a>

Be sure to visit the official documentation at <a href="https://devdojo.com/auth/docs" target="_blank">https://devdojo.com/auth/docs</a>

## Installation

You can install this package into any new Laravel application, or any of the available <a href="https://devdojo.com/auth/docs/install" target="_blank">Laravel Starter Kits</a>.

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

Finally extend the Devdojo User Model:

```
use Devdojo\Auth\Models\User as AuthUser;

class User extends AuthUser
```

in your `App\Models\User` model. 

Now, you're ready to rock! Auth has just been installed and you'll be able to visit the following authentication routes:

 - Login (project.test/auth/login)
 - Register (project.test/auth/register)
 - Forgot Password (project.test/auth/register)
 - Password Reset (project.test/auth/password/reset)
 - Password Reset Token (project.test/auth/password/ReAlLyLoNgPaSsWoRdReSeTtOkEn)
 - Password Confirmation (project.test/auth/password/confirm)
 - Two-Factor Challenge (project.test/auth/two-factor-challenge)
  
You'll also have access to the Two Factor Setup page

 - Two-Factor Setup (project.test/user/two-factor-authentication)

When you need to logout, you can visit the Logout route

- Logout Route (project.test/auth/logout)

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

## (Optional) Enabling Passkeys

Passkeys are disabled by default. To enable passwordless sign-in:

1. Publish the passkeys config:

```
php artisan vendor:publish --tag=auth:passkeys-config
```

2. Publish and run the passkeys migration:

```
php artisan vendor:publish --tag=passkeys-migrations --provider="Laravel\Passkeys\PasskeysServiceProvider"
php artisan migrate
```

3. Visit `/auth/setup/passkeys` and enable **Passkey Sign-In**.

Your `App\Models\User` should extend `Devdojo\Auth\Models\User`, which already includes the required `PasskeyAuthenticatable` trait.

## Upgrading from 2.x

See [UPGRADE.md](UPGRADE.md) for the v3.0 migration guide.

## License

The DevDojo Auth package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
