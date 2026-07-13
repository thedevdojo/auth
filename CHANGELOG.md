# Changelog

## 3.0.0 - Unreleased

### Breaking Changes

- Requires Livewire 4 (`^4.0`). Livewire 3 is no longer supported.
- Removed `livewire/volt` and `laravel/folio` dependencies.
- Auth pages are now Livewire 4 single-file components registered via `Livewire::addLocation()`.
- Minimum PHP version is now 8.2.
- Minimum Laravel version is now 12.

### Added

- Optional passkey authentication via `laravel/passkeys`.
- New `/auth/setup/passkeys` configuration screen to enable or disable passkey sign-in.
- `enable_passkeys` setting in `config/devdojo/auth/settings.php`.
- `auth:passkeys-config` publish tag for passkeys configuration.

### Changed

- Migrated all authentication pages from Volt/Folio to Livewire 4 SFCs.
- `Devdojo\Auth\Models\User` now implements `PasskeyUser` and uses `PasskeyAuthenticatable`.
