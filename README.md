# TALLium

A streamlined [TALL stack](https://tallstack.dev) starter kit for Laravel, built on Livewire single-file components. Forked from the official [Laravel Livewire starter kit](https://github.com/laravel/livewire-starter-kit) and refined for clarity -- especially when working with AI coding agents.

## What's Included

- **Laravel 13** with [Fortify](https://laravel.com/docs/fortify) authentication (login, registration, password reset, email verification, two-factor auth, passkeys)
- **[Chisel](https://github.com/laravel/chisel)** build-time feature toggles -- `install:features` lets consumers opt in/out of registration, email verification, 2FA, passkeys, and password confirmation, same as the official starter kit
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
- **Chisel adopted as-is** -- `chisel.php` and `chisel-paths.php` are kept and retargeted at TALLium's consolidated `pages/` structure, so the `install:features` toggle prompt (registration, email verification, 2FA, passkeys, password confirmation) still works exactly as it does in the official kit.

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
chisel.php                                  # Feature-toggle script (install:features)
chisel-paths.php                            # File-path overrides for chisel, retargeted at pages/
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
      security.blade.php                    # Security settings (password + 2FA + passkeys)
      appearance.blade.php                  # Appearance settings (Livewire)
      container.blade.php                   # Settings layout wrapper
      delete-user-form.blade.php            # Account deletion trigger + modal wrapper
      delete-user-modal.blade.php           # Account deletion confirmation (Livewire)
      two-factor-setup-modal.blade.php      # 2FA enrollment modal (Livewire)
      two-factor/recovery-codes.blade.php   # 2FA recovery codes (Livewire)
      partials/heading.blade.php            # Settings page header
  components/
    app-logo.blade.php                      # Sidebar brand component
    app-logo-icon.blade.php                 # Dynamic monogram icon
    desktop-user-menu.blade.php             # User dropdown menu
    passkey-registration.blade.php          # Passkey enrollment (Alpine + WebAuthn)
    passkey-verify.blade.php                # Passkey verification prompt
```

## License

MIT

---

## Building TALLium from the Official Starter Kit

This section describes every change needed to transform the official
[Laravel Livewire starter kit](https://github.com/laravel/livewire-starter-kit) into TALLium.
Apply these steps in order to a fresh clone of the upstream kit.

**Goal:** Consolidate layout variants, enforce a single layout per context, move all pages under
`resources/views/pages/`, consolidate routes into `routes/web.php`, replace the hardcoded brand
SVG with a dynamic monogram, remove indirection (Logout action class, multiple layout shims), and
adopt upstream's chisel feature-toggle system (and passkeys) as-is, retargeted at the consolidated
file layout.

### Overview of changes

| Kind     | Count | Summary |
|----------|-------|---------|
| Delete   | 7     | Logout action, 2 app layout variants, 3 auth layout variants, settings route file |
| Rename   | 5     | head partial, dashboard, settings layout→container, settings heading, welcome |
| Modify   | 12    | .gitignore, composer.json, seeder, 2 components, 2 layouts, 3 settings pages, routes/web.php, chisel.php, chisel-paths.php |
| Create   | 1     | `layouts/guest.blade.php` |

Chisel and passkeys themselves are **not** TALLium-specific additions -- they ship with the
official starter kit as of the versions this recipe targets. The only TALLium-specific work is
retargeting `chisel.php` / `chisel-paths.php` at the consolidated `pages/` layout (Step 8) and
fixing the one file the original Logout-removal step missed (Step 3).

---

### Step 1 — Delete files

Remove these files entirely (they are replaced by inlined or consolidated alternatives):

```
app/Livewire/Actions/Logout.php
resources/views/layouts/app/header.blade.php
resources/views/layouts/app/sidebar.blade.php
resources/views/layouts/auth/card.blade.php
resources/views/layouts/auth/simple.blade.php
resources/views/layouts/auth/split.blade.php
routes/settings.php
```

```bash
git rm app/Livewire/Actions/Logout.php
git rm resources/views/layouts/app/header.blade.php
git rm resources/views/layouts/app/sidebar.blade.php
git rm resources/views/layouts/auth/card.blade.php
git rm resources/views/layouts/auth/simple.blade.php
git rm resources/views/layouts/auth/split.blade.php
git rm routes/settings.php
```

**Why:** The `Logout` action class is replaced by an inline form in the layout. The three auth
layout variants (card/simple/split) are collapsed into a single `auth.blade.php`. The two app
layout variants (header/sidebar) are collapsed into a single `app.blade.php`. Settings routes are
merged into `routes/web.php`.

**Gotcha:** `resources/views/pages/settings/delete-user-modal.blade.php` also constructor-injects
`App\Livewire\Actions\Logout` in its `deleteUser()` method. Deleting `Logout.php` breaks it — see
the fix in Step 3.

---

### Step 2 — Rename files (preserving git history)

First create the destination directories:

```bash
mkdir -p resources/views/layouts/partials
mkdir -p resources/views/pages/settings/partials
```

Then rename:

```bash
git mv resources/views/partials/head.blade.php \
       resources/views/layouts/partials/head.blade.php

git mv resources/views/dashboard.blade.php \
       resources/views/pages/dashboard.blade.php

git mv resources/views/pages/settings/layout.blade.php \
       resources/views/pages/settings/container.blade.php

git mv resources/views/partials/settings-heading.blade.php \
       resources/views/pages/settings/partials/heading.blade.php

git mv resources/views/welcome.blade.php \
       resources/views/pages/welcome.blade.php
```

The last rename (welcome) also requires content changes — covered in Step 5.

---

### Step 3 — Modify small files

#### `.gitignore`

Add one line at the end:

```diff
+/.claude
```

#### `composer.json`

```diff
-    "name": "laravel/livewire-starter-kit",
+    "name": "smwks/tallium",
     "type": "project",
-    "description": "The official Laravel starter kit for Livewire.",
+    "description": "A TALL stack starter kit, focus on livewire single file components. AI coding friendly.",
```

```diff
-        "livewire/livewire": "^4.1"
+        "livewire/livewire": "^4.2"
```

```diff
     "require-dev": {
         "fakerphp/faker": "^1.24",
+        "laravel/boost": "^2",
         "laravel/pail": "^1.2.5",
         "laravel/pint": "^1.27",
-        "laravel/sail": "^1.53",
         "mockery/mockery": "^1.6",
```

#### `resources/views/pages/settings/delete-user-modal.blade.php`

Replaces the injected `Logout` action (deleted in Step 1) with the same three calls it made,
inlined directly in `deleteUser()`:

```diff
 use App\Concerns\PasswordValidationRules;
-use App\Livewire\Actions\Logout;
 use Illuminate\Support\Facades\Auth;
+use Illuminate\Support\Facades\Session;
 use Livewire\Component;

 new class extends Component {
     use PasswordValidationRules;

     public string $password = '';

-    public function deleteUser(Logout $logout): void
+    public function deleteUser(): void
     {
         $this->validate([
             'password' => $this->currentPasswordRules(),
         ]);

-        tap(Auth::user(), $logout(...))->delete();
+        $user = Auth::user();
+
+        Auth::guard('web')->logout();
+
+        Session::invalidate();
+        Session::regenerateToken();
+
+        $user->delete();

         $this->redirect('/', navigate: true);
     }
 }; ?>
```

#### `database/seeders/DatabaseSeeder.php`

Add an explicit password so the dev seeder produces a known credential:

```diff
         User::factory()->create([
             'name' => 'Test User',
             'email' => 'test@example.com',
+            'password' => bcrypt('password'),
         ]);
```

#### `resources/views/components/app-logo-icon.blade.php`

Replace the hardcoded brand SVG with a dynamic two-letter monogram generated from `config('app.name')`:

```diff
-<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" {{ $attributes }}>
-    <path
-        fill="currentColor"
-        fill-rule="evenodd"
-        clip-rule="evenodd"
-        d="M17.2 5.633 8.6.855 0 5.633v26.51l16.2 9 ..."
-    />
-</svg>
+<?php
+$letters = Str::of(config('app.name'))->substr(0, 2)->upper();
+?>
+<svg {{ $attributes }} viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-label="App Logo">
+    <rect x="4" y="4" width="56" height="56" rx="12" stroke="currentColor" stroke-width="2.5"/>
+    <text x="32" y="36" text-anchor="middle" dominant-baseline="central" font-family="ui-sans-serif, system-ui, sans-serif" font-size="26" font-weight="700" fill="currentColor">{{ $letters }}</text>
+</svg>
```

#### `resources/views/components/app-logo.blade.php`

Remove the `@props`/`@if` toggle. The component is always used in sidebar context now, so always
renders `flux:sidebar.brand`. Name comes from `config('app.name')` rather than hardcoded:

```diff
-@props([
-    'sidebar' => false,
-])
-
-@if($sidebar)
-    <flux:sidebar.brand name="Laravel Starter Kit" {{ $attributes }}>
-        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
-            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
-        </x-slot>
-    </flux:sidebar.brand>
-@else
-    <flux:brand name="Laravel Starter Kit" {{ $attributes }}>
-        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
-            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
-        </x-slot>
-    </flux:brand>
-@endif
+<flux:sidebar.brand name="{{ config('app.name') }}" {{ $attributes }}>
+    <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground">
+        <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
+    </x-slot>
+</flux:sidebar.brand>
```

#### `resources/views/pages/settings/appearance.blade.php`

Two substitutions — updated include path and component name:

```diff
-    @include('partials.settings-heading')
+    @include('pages.settings.partials.heading')

     <flux:heading class="sr-only">{{ __('Appearance settings') }}</flux:heading>

-    <x-pages::settings.layout ...>
+    <x-pages::settings.container ...>
         ...
-    </x-pages::settings.layout>
+    </x-pages::settings.container>
```

#### `resources/views/pages/settings/profile.blade.php`

Same two substitutions:

```diff
-    @include('partials.settings-heading')
+    @include('pages.settings.partials.heading')

     <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

-    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
+    <x-pages::settings.container :heading="__('Profile')" :subheading="__('Update your name and email address')">
         ...
-    </x-pages::settings.layout>
+    </x-pages::settings.container>
```

#### `resources/views/pages/settings/security.blade.php`

Same two substitutions:

```diff
-    @include('partials.settings-heading')
+    @include('pages.settings.partials.heading')

     <flux:heading class="sr-only">{{ __('Security settings') }}</flux:heading>

-    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
+    <x-pages::settings.container :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
         ...
-    </x-pages::settings.layout>
+    </x-pages::settings.container>
```

---

### Step 4 — Replace layout files

#### `resources/views/layouts/auth.blade.php`

The old file was a one-line shim delegating to a vendor component (`x-layouts::auth.simple`).
Replace it with a self-contained HTML document:

```diff
-<x-layouts::auth.simple :title="$title ?? null">
-    {{ $slot }}
-</x-layouts::auth.simple>
```

Full replacement content:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
<div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="flex w-full max-w-sm flex-col gap-2">
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
            <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
            </span>
            <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
        </a>
        <div class="flex flex-col gap-6">
            {{ $slot }}
        </div>
    </div>
</div>
@fluxScripts
</body>
</html>
```

Note: `@include('layouts.partials.head')` resolves to `resources/views/layouts/partials/head.blade.php`
(the renamed partial from Step 2).

#### `resources/views/layouts/app.blade.php`

The old file was a one-line shim delegating to `x-layouts::app.sidebar`.
Replace it with a self-contained HTML document. Key additions vs the old vendor sidebar layout:
- `@include('layouts.partials.head')` instead of `@include('partials.head')`
- Lower nav section (commented out placeholder)
- Mobile user dropdown uses inline logout form (no `Logout` action class)
- `@persist('toast')` block with `<flux:toast />` so Flux toast notifications work

```diff
-<x-layouts::app.sidebar :title="$title ?? null">
-    <flux:main>
-        {{ $slot }}
-    </flux:main>
-</x-layouts::app.sidebar>
```

Full replacement content:

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('layouts.partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.header>
        <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
        <flux:sidebar.collapse class="lg:hidden" />
    </flux:sidebar.header>

    <flux:sidebar.nav>
        <flux:sidebar.group :heading="__('Platform')" class="grid">
            <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </flux:sidebar.item>
        </flux:sidebar.group>
    </flux:sidebar.nav>

    <flux:spacer />

    <flux:sidebar.nav>
        {{--
        <flux:sidebar.item icon="folder-git-2" href="#" target="_blank">
            {{ __('Lower Navigation Item') }}
        </flux:sidebar.item>
        --}}

    </flux:sidebar.nav>

    <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
</flux:sidebar>


<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
        <flux:profile
            :initials="auth()->user()->initials()"
            icon-trailing="chevron-down"
        />

        <flux:menu>
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                        />

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                    {{ __('Settings') }}
                </flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:header>

<flux:main>
    {{ $slot }}
</flux:main>

@persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
@endpersist

@fluxScripts
</body>
</html>
```

---

### Step 5 — Modify `resources/views/pages/welcome.blade.php`

> This file was renamed from `resources/views/welcome.blade.php` in Step 2.

The original file is a self-contained HTML document (DOCTYPE through `</html>`). After the rename,
strip the entire HTML wrapper — everything from `<!DOCTYPE html>` down through and including the
`<body class="...">` opening tag — and replace it with `<x-layouts::guest>`. Similarly replace
the closing `</body>\n</html>` with `</x-layouts::guest>`.

The body content (header nav, main SVG artwork, footer) is **unchanged**.

```diff
-<!DOCTYPE html>
-<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
-    <head>
-        ...fonts, favicon, inline <style> block...
-    </head>
-    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
+<x-layouts::guest>
         <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
             ...
         </header>
         <main>
             ...
         </main>
         @if (Route::has('login'))
             <div class="h-14.5 hidden lg:block"></div>
         @endif
-    </body>
-</html>
+</x-layouts::guest>
```

Scripted transformation (Python):

```python
with open('resources/views/pages/welcome.blade.php', 'r') as f:
    content = f.read()

body_start = content.find('<body ')
body_end = content.find('>\n', body_start) + 2
content = '<x-layouts::guest>\n' + content[body_end:]
content = content.replace('    </body>\n</html>\n', '</x-layouts::guest>\n')

with open('resources/views/pages/welcome.blade.php', 'w') as f:
    f.write(content)
```

---

### Step 6 — Create `resources/views/layouts/guest.blade.php`

Extract the HTML wrapper that was removed from `welcome.blade.php` (Step 5) and use it as the new
guest layout. The wrapper is the HTML document structure including the pre-built inline CSS that the
welcome page ships with — it does **not** use `@vite` because the welcome page CSS is pre-built and
bundled inline for zero-dependency serving.

```python
with open('resources/views/welcome.blade.php', 'r') as f:
    content = f.read()

body_start = content.find('<body ')
body_end = content.find('>\n', body_start) + 2
header = content[:body_end]

with open('resources/views/layouts/guest.blade.php', 'w') as f:
    f.write(header + '    {{ $slot }}\n</body>\n</html>\n')
```

> **Order matters:** run the script for `guest.blade.php` **before** running the script for
> `welcome.blade.php`, or keep a copy of the original file. Once the wrapper is stripped from
> `welcome.blade.php` the source for the inline styles is gone.

The resulting `guest.blade.php` has this shape (CSS block abbreviated):

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <style>
            /* pre-built inline Tailwind CSS — copied verbatim from original welcome.blade.php */
        </style>
    </head>
    <body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    {{ $slot }}
</body>
</html>
```

---

### Step 7 — Replace `routes/web.php`

The old file required `routes/settings.php` (deleted in Step 1) and used bare view names.
Replace entirely with the consolidated version that uses `pages.*` view names, inlines all
settings routes, and keeps upstream's `@chisel-*` markers so `install:features` can still strip
the password-confirmation middleware and the passkeys route when those features are disabled:

```diff
 <?php

 use Illuminate\Support\Facades\Route;

-Route::view('/', 'welcome')->name('home');
+Route::view('/', 'pages.welcome')->name('home');

-Route::middleware(['auth', 'verified'])->group(function () {
-    Route::view('dashboard', 'dashboard')->name('dashboard');
-});
+Route::view('dashboard', 'pages.dashboard')
+    ->middleware(['auth', 'verified'])
+    ->name('dashboard');
+
+Route::middleware(['auth'])->group(function () {
+    Route::redirect('settings', 'settings/profile');
+
+    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
+});
+
+Route::middleware(['auth', 'verified'])->group(function () {
+    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

-require __DIR__.'/settings.php';
+    Route::livewire('settings/security', 'pages::settings.security')
+        /* @chisel-password-confirmation */
+        ->middleware([
+            'password.confirm',
+        ])
+        /* @end-chisel-password-confirmation */
+        ->name('security.edit');
+});
+
+/* @chisel-passkeys */
+Route::get('.well-known/passkey-endpoints', function () {
+    return response()->json([
+        'enroll' => route('security.edit'),
+        'manage' => route('security.edit'),
+    ]);
+})->name('well-known.passkeys');
+/* @end-chisel-passkeys */
```

---

### Step 8 — Adapt chisel to TALLium's file layout

Upstream ships `chisel.php` and `chisel-paths.php` at the project root, driving the
`install:features {--answers=}` command. Both encode assumptions from upstream's
Single-File-Component / Multi-File-Component folder layouts that don't hold once pages move
under `resources/views/pages/` (Step 2/5) and settings routes are inlined into `routes/web.php`
(Step 1/7). Two fixes are required — everything else in chisel works unmodified.

#### `chisel-paths.php`

Only the `welcome` entry is stale (it still points at the pre-Step-2 location):

```diff
-    'welcome' => 'resources/views/welcome.blade.php',
+    'welcome' => 'resources/views/pages/welcome.blade.php',
```

#### `chisel.php`

The `2fa`, `passkeys`, and `password-confirmation` `->selected(...)` closures hardcode
`'routes/settings.php'` in six places (both the `then:` and `else:` branches) — a file deleted in
Step 1. Point them at the consolidated `routes/web.php` instead:

```bash
sed -i '' "s/'routes\/settings\.php'/'routes\/web.php'/g" chisel.php
```

**Why:** Without these two fixes, `install:features` throws when it tries to
`removeSection`/`removeSectionMarkers` on a path that no longer exists (`routes/settings.php`), or
silently no-ops on the `welcome` chisel markers because it's looking at the wrong file. Nothing
else about chisel's behavior changes — toggles, defaults, and the `@chisel-*` marker names are
adopted as-is from upstream.

---

### Summary of final layout structure

| Layout file | Used by | Includes |
|---|---|---|
| `layouts/app.blade.php` | All authenticated pages | `layouts/partials/head` · Flux sidebar · mobile user menu · `@persist('toast')` |
| `layouts/auth.blade.php` | Login, register, password reset, 2FA | `layouts/partials/head` |
| `layouts/guest.blade.php` | Welcome / public pages | Pre-built inline CSS (no Vite) |
| `layouts/partials/head.blade.php` | Included by `app` and `auth` layouts | Vite assets, meta tags |

### Gotcha: `<flux:toast />` must be in the app layout

The `Flux::toast()` PHP helper dispatches a Livewire event. The `<flux:toast />` component must be
present in the layout to receive it. The old vendor sidebar layout included this inside
`@persist('toast')`. When inlining the layout, it is easy to forget:

```blade
@persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
@endpersist
```

This must appear in `layouts/app.blade.php` **after** `<flux:main>` and before `@fluxScripts`.
Without it, calls like `Flux::toast(variant: 'success', text: 'Saved.')` produce no visible output.
