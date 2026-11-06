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

## Using the login and registration flows in your own component

The pages at `/auth/login` and `/auth/register` are views over two traits you can wear on any Livewire component — a modal, a sidebar, an inline form on a landing page:

```php
use Devdojo\Auth\Traits\AuthenticatesUsers;

class LoginForm extends \Livewire\Component
{
    use AuthenticatesUsers; // $email, $password, $rememberMe, authenticate(), editIdentity()

    protected function passwordOnSameScreen(): bool { return true; } // email + password together

    protected function authenticated($user) { return redirect('/app'); } // optional landing override
}
```

`RegistersUsers` is the same idea for sign-up (`$name`, `$email`, `$password`, `register()`), with `registrationData()`, `createUser()` and `registered($user)` as the seams to shape the account or the landing. Both honour every setting in `config/devdojo/auth/settings.php`; `login_show_password_same_screen` switches the package's own login page to the one-screen flow.

## Running the test suite

The tests in this package are designed to run **from inside a consuming Laravel application**, not from the package folder. Set up a host app as described in [CONTRIBUTING.md](CONTRIBUTING.md): require the package via a path repository, then replace the app's `tests/` directory with a symlink to this package's `tests/` folder. From the app root you can then run the Feature suite with `./vendor/bin/pest` and the Browser suite with Laravel Dusk.

Running `./vendor/bin/pest` standalone from this package's root is **expected to fail** with:

```
Pest\Exceptions\TestCaseClassOrTraitNotFound
The class `DuskTestCase` was not found.
```

That's by design, not a broken install:

- The test classes live in the `Tests\` namespace and rely on the host app's `Tests\` → `tests/` autoload mapping. The package's own composer autoload maps `Devdojo\Auth\Tests\` to `tests/`, so `Tests\DuskTestCase` and `Tests\TestCase` never resolve standalone.
- `Tests\TestCase` extends the framework's application test case, which boots the host app's `bootstrap/app.php` — there is no bootable application inside the package.
- The `tests/Browser` suite is Laravel Dusk and needs a real running application plus ChromeDriver.

## License

The DevDojo Auth package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
