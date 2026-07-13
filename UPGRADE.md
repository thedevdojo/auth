# Upgrading to 3.0

Version 3.0 is a breaking release. Plan for dependency updates, asset republishing, and page customizations to move from Volt/Folio to Livewire 4.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Livewire 4 (`^4.0`)

## Upgrade steps

### 1. Update dependencies

```bash
composer require devdojo/auth:^3.0 livewire/livewire:^4.0
```

Volt and Folio are no longer required by this package. You may keep them in your app for unrelated features.

`laravel/passkeys` is included as a dependency. Passkey UI remains disabled until you enable it in `/auth/setup/passkeys`.

### 2. Republish package files

```bash
php artisan vendor:publish --tag=auth:config
php artisan vendor:publish --tag=auth:assets --force
php artisan vendor:publish --tag=auth:migrations
php artisan migrate
```

Republishing `auth:assets` is required after upgrading. The setup UI uses Tailwind v4 class names in `public/auth/build/assets/styles.css`.

### 3. Update customized auth pages

Auth screens are now Livewire 4 single-file components registered from the package via `Livewire::addLocation()`.

If you previously customized Volt/Folio pages under `resources/views/pages/auth/`, move them to the same paths and register an additional Livewire location in your app service provider:

```php
use Livewire\Livewire;

Livewire::addLocation(viewPath: resource_path('views/pages'));
```

Your application views take precedence when registered after the package provider.

To override Blade components without copying entire pages, publish to `resources/views/vendor/auth/`.

### 4. Optional: enable passkeys

```bash
php artisan vendor:publish --tag=auth:passkeys-config
php artisan vendor:publish --tag=passkeys-migrations --provider="Laravel\Passkeys\PasskeysServiceProvider"
php artisan migrate
```

Then visit `/auth/setup/passkeys` and enable **Passkey Sign-In**.

Your `App\Models\User` should extend `Devdojo\Auth\Models\User`, which already includes `PasskeyAuthenticatable`.

### 5. Clear caches

```bash
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## What changed

| Area | v2.x | v3.0 |
| --- | --- | --- |
| Page routing | Volt + Folio | Livewire 4 SFCs |
| Livewire | 3.x | 4.x |
| Passkeys | Not included | Optional via `enable_passkeys` |
| Setup preview modal | `absolute` + custom z-index | `fixed` + standard z-index utilities |

## Need help?

Open a discussion at [devdojo.com/questions](https://devdojo.com/questions) or review the [documentation](https://devdojo.com/auth/docs).
