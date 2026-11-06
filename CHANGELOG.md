# Changelog

All notable changes to `auth` will be documented in this file

## Unreleased

- The login and registration flows now live in two traits any Livewire component can wear — `Devdojo\Auth\Traits\AuthenticatesUsers` and `Devdojo\Auth\Traits\RegistersUsers` — so an app can put them in a modal or an inline form with its own markup. The `auth/login` and `auth/register` pages run on them (same state names, same behaviour). Hooks: `authenticated($user)`, `registered($user)`, `registrationData()`, `createUser()`, `passwordOnSameScreen()`.
- New setting `login_show_password_same_screen` (default `false`): show the email and password fields together instead of the email-first two-step login; the social-only account check then runs on submit.

## 1.0.0 - 201X-XX-XX

- initial release
