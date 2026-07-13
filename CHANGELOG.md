# Changelog

## 3.0.0 - Unreleased

### Breaking Changes

- Requires Livewire 4 (`^4.0`). Livewire 3 is no longer supported.
- Removed `livewire/volt` and `laravel/folio` dependencies.
- Auth pages are now Livewire 4 single-file components registered via `Livewire::addLocation()`.
- Minimum PHP version is now 8.2.
- Minimum Laravel version is now 12 (Laravel 13 is also supported).
- Published auth assets must be republished after upgrading. See [UPGRADE.md](UPGRADE.md).

### Added

- Optional passkey authentication via `laravel/passkeys`.
- New `/auth/setup/passkeys` configuration screen to enable or disable passkey sign-in.
- `enable_passkeys` setting in `config/devdojo/auth/settings.php`.
- `auth:passkeys-config` publish tag for passkeys configuration.

### Changed

- Migrated all authentication pages from Volt/Folio to Livewire 4 SFCs.
- `Devdojo\Auth\Models\User` now implements `PasskeyUser` and uses `PasskeyAuthenticatable`.
- Setup preview modal now uses `fixed` positioning so it renders above the settings panel.

### Fixed

- Setup preview modal no longer appears behind the settings page when published assets are current.
