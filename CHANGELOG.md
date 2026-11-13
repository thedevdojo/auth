# Changelog

All notable changes to `auth` will be documented in this file

## 2.4.0 - 2026-11-05

- The login and registration flows now live in two traits any Livewire component can wear — `Devdojo\Auth\Traits\AuthenticatesUsers` and `Devdojo\Auth\Traits\RegistersUsers` — so an app can put them in a modal or an inline form with its own markup. The `auth/login` and `auth/register` pages run on them (same state names, same behaviour). Hooks: `authenticated($user)`, `registered($user)`, `registrationData()`, `createUser()`, `passwordOnSameScreen()`.
- New setting `login_show_password_same_screen` (default `false`): show the email and password fields together instead of the email-first two-step login; the social-only account check then runs on submit.

## Earlier releases

See the [GitHub releases](https://github.com/thedevdojo/auth/releases).
