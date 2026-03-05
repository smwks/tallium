# TALLium

A streamlined [TALL stack](https://tallstack.dev) starter kit for Laravel, built on Livewire single-file components. Forked from the official [Laravel Livewire starter kit](https://github.com/laravel/livewire-starter-kit) and refined for clarity -- especially when working with AI coding agents.

## What's Included

- **Laravel 12** with [Fortify](https://laravel.com/docs/fortify) authentication (login, registration, password reset, email verification, two-factor auth)
- **Livewire 4** with single-file page components (`Route::livewire()`)
- **[Flux UI](https://fluxui.dev)** component library
- **Tailwind CSS 4** with dark mode
- **TypeScript** and **Vite**

## What Changed From the Official Starter Kit

TALLium consolidates the official starter kit to reduce pattern variability and improve AI coding clarity:

- **Single layout per context** -- one `app.blade.php` (authenticated), one `auth.blade.php` (auth forms), one `guest.blade.php` (public). No layout variants (sidebar/header, card/simple/split).
- **Layouts are self-contained** -- each layout is a complete HTML document. No scattered partials to trace through.
- **Pages live in `resources/views/pages/`** -- routed via `Route::livewire()` or `Route::view()`. Consistent, predictable file locations.
- **Routes consolidated into `routes/web.php`** -- no separate `routes/settings.php`.
- **Dynamic brand logo** -- generates a monogram from `config('app.name')` instead of a hardcoded SVG.
- **Removed `app/Livewire/Actions/`** -- logout is handled inline. Less indirection.

## Getting Started

```bash
# Clone and install
laravel new my-app --using=smwks/tallium
```

The `composer setup` script handles: dependency installation, `.env` creation, key generation, database migration, npm install, and asset build.

### Development Server

```bash
composer dev
```

Starts the Laravel dev server, queue worker, log tail (Pail), and Vite in parallel.

Default test credentials (from seeder):
- **Email:** test@example.com
- **Password:** password

### Other Commands

```bash
composer lint          # Fix code style with Pint
composer lint:check    # Check code style without fixing
composer test          # Run linter + test suite
```

## Project Structure

```
routes/web.php                              # All routes
resources/views/
  layouts/
    app.blade.php                           # Authenticated layout (sidebar nav)
    auth.blade.php                          # Auth form layout (login, register, etc.)
    guest.blade.php                         # Public/guest layout
    partials/head.blade.php                 # Shared <head> content
  pages/
    welcome.blade.php                       # Landing page
    dashboard.blade.php                     # Dashboard
    settings/
      profile.blade.php                     # Profile settings (Livewire)
      password.blade.php                    # Password settings (Livewire)
      two-factor.blade.php                  # 2FA settings (Livewire)
      appearance.blade.php                  # Appearance settings (Livewire)
      container.blade.php                   # Settings layout wrapper
      delete-user-form.blade.php            # Account deletion form
      partials/heading.blade.php            # Settings page header
  components/
    app-logo.blade.php                      # Sidebar brand component
    app-logo-icon.blade.php                 # Dynamic monogram icon
    desktop-user-menu.blade.php             # User dropdown menu
```

## License

MIT
