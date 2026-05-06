# Tulong Kabataan

Tulong Kabataan is a Laravel-based community support platform for organizing donations, campaigns, volunteer events, and youth-focused community initiatives in Bicol. The system gives donors, volunteers, campaign creators, and administrators one place to manage fundraising, in-kind donations, event participation, account verification, and impact reporting.

Live website: **https://tulongkabataanbicol.com/**

## Core Features

- **Landing and public pages** for introducing the platform, featured campaigns, impact highlights, and trust information.
- **Authentication** with registration, login, Google OAuth, email verification, forgot password, and reset password flows.
- **Campaign management** for creating campaigns, viewing campaign details, receiving donations, tracking campaign progress, and exporting donation records.
- **In-kind donation management** for item donations, drop-off locations, donation tracking, category charts, and impact reports.
- **Event management** for volunteer events, event registration, volunteer roles, event status updates, and attendance/participation tracking.
- **User profile dashboard** for managing personal details, identity verification, campaigns, donations, events, and in-kind records.
- **Administrator dashboard** for account verification, campaign review, donation review, event management, in-kind location management, DNC records, charts, and reports.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade, Tailwind CSS 4, Vite
- **Interactivity:** JavaScript, Axios, Livewire
- **Authentication:** Laravel auth/session flow, Laravel Socialite for Google login
- **Reports/PDF:** Dompdf
- **Charts and UI libraries:** ECharts, Chart.js, Remixicon, Bootstrap icons where used
- **Database:** Configurable through `.env`

## Local Setup

1. Install PHP dependencies:

```bash
composer install
```

2. Install frontend dependencies:

```bash
npm install
```

3. Create your local environment file:

```bash
copy .env.example .env
```

On macOS/Linux:

```bash
cp .env.example .env
```

4. Fill in the required `.env` values, especially database, mail, app URL, and Google OAuth credentials if Google login is needed.

5. Generate the Laravel app key:

```bash
php artisan key:generate
```

6. Run migrations:

```bash
php artisan migrate
```

7. Build frontend assets:

```bash
npm run build
```

8. Start the local server:

```bash
php artisan serve
```

For active frontend development, run Vite:

```bash
npm run dev
```

## Useful Commands

```bash
php artisan serve
```

Starts the Laravel development server.

```bash
npm run dev
```

Starts the Vite development server.

```bash
npm run build
```

Builds production frontend assets into `public/build`.

```bash
php artisan test
```

Runs the Laravel test suite.

```bash
php artisan config:clear
```

Clears cached configuration after `.env` changes.

## Environment Files

- `.env` is the local private environment file. It should contain real credentials and must not be committed.
- `.env.example` is the public template. It contains the same keys as `.env` but empty values so other developers know what to configure.

## Route Organization

This project uses multiple route files instead of a single `routes/web.php`. They are loaded in `bootstrap/app.php`.

- `routes/login.php` handles landing page, login, registration, Google OAuth, email verification, forgot password, and reset password routes.
- `routes/profile.php` handles user profile, identity verification, profile dashboard, campaign owner tools, event history, and in-kind donation history.
- `routes/campaign.php` handles public campaign listing, campaign details, campaign creation, donations, and notifications.
- `routes/event.php` handles public event pages, registration, event details, and live event update endpoints.
- `routes/inkind.php` handles public in-kind donation pages, donation submission, tracking, stats, and impact report APIs.
- `routes/administrator.php` handles administrator login, dashboard, account verification, campaigns, events, in-kind donations, locations, reports, and DNC records.
- `routes/console.php` is reserved for Artisan console routes.

## Folder Guide

### Root Files

- `.env.example` defines the required environment keys with empty values.
- `artisan` is Laravel's command-line entry point.
- `composer.json` lists PHP dependencies, Laravel scripts, autoloading, and test commands.
- `package.json` lists frontend tooling such as Vite and Tailwind CSS.
- `vite.config.js` configures Laravel Vite asset building.
- `phpunit.xml` configures the Laravel test runner.
- `README.md` contains this project documentation.

### `app/`

Contains the Laravel application code.

- `app/Http/Controllers/` contains the main request handlers: authentication, profile, campaign, event, in-kind, and administrator logic.
- `app/Models/` contains Eloquent models such as users, campaigns, donations, events, drop-off points, verification requests, and impact reports.
- `app/Jobs/` contains queued/background work if the application needs async processing.
- `app/Livewire/` contains Livewire components.
- `app/Mail/` contains mail classes for email features.
- `app/Notifications/` contains Laravel notification classes.
- `app/Providers/` contains service providers that bootstrap application services.

### `bootstrap/`

Contains Laravel bootstrap files.

- `bootstrap/app.php` configures the application, route files, middleware, exception handling, and health route.
- `bootstrap/cache/` is used by Laravel for cached framework files.

### `config/`

Contains Laravel configuration files for app settings, database, sessions, filesystems, mail, cache, queues, and services. Most values are powered by `.env`.

### `database/`

Contains database-related files.

- `database/migrations/` defines the tables for users, campaigns, donations, events, notifications, admin accounts, and impact reports.
- `database/factories/` contains model factories for test or seed data.
- `database/seeders/` contains seeders for inserting starter records.

### `public/`

Contains publicly accessible files served by the web server.

- `public/img/` stores public images and branding assets.
- `public/js/` stores page-specific JavaScript that is not bundled through Vite.
- `public/build/` stores generated production assets from `npm run build`.

There is no `public/css` folder anymore. Project CSS is handled through `resources/css/app.css` and Vite.

### `resources/`

Contains frontend source files.

- `resources/views/` contains Blade templates for all pages.
- `resources/css/app.css` is the main Tailwind/Vite CSS entrypoint.
- `resources/css/layout/` contains shared layout styling such as the main header.
- `resources/css/pages/` contains modular page styles grouped by feature area.
- `resources/css/admin/` contains administrator page styles.
- `resources/js/` contains Vite-managed JavaScript entry files.

### `resources/views/`

Blade templates are grouped by feature.

- `resources/views/login/` contains login and register pages.
- `resources/views/auth/` contains email verification and reset password pages.
- `resources/views/campaign/` contains campaign listing, campaign detail, and campaign creation pages.
- `resources/views/event/` contains public event pages and registration views.
- `resources/views/inkind/` contains in-kind donation pages, modals, and tracking views.
- `resources/views/profile/` contains user profile, account verification, dashboard, event history, and in-kind history pages.
- `resources/views/administrator/` contains admin dashboard pages for accounts, campaigns, events, in-kind donations, and DNC records.
- `resources/views/partials/` contains shared public partials such as headers, footers, and modals.
- `resources/views/emails/` contains email templates.
- `resources/views/livewire/` contains Livewire view templates.

### `resources/css/`

CSS is modular but still compiled through one Vite entrypoint.

- `resources/css/app.css` imports Tailwind, global fonts, shared styles, and all CSS modules.
- `resources/css/layout/main-header.css` styles the shared public header.
- `resources/css/pages/profile/` styles profile dashboard, profile events, profile in-kind, and main profile pages.
- `resources/css/pages/campaign/` styles campaign list, campaign detail, and campaign creation pages.
- `resources/css/pages/event/` styles public event pages.
- `resources/css/pages/inkind/` styles public in-kind pages and modals.
- `resources/css/admin/` styles administrator account, event, and in-kind management pages.

The CSS modules are scoped by page body classes where possible to reduce conflicts between pages.

### `routes/`

Contains route files grouped by domain instead of one large route file. This keeps authentication, profile, campaign, event, in-kind, and admin routes easier to maintain.

### `storage/`

Stores generated and uploaded runtime files.

- `storage/app/` stores application files.
- `storage/framework/` stores cache, sessions, compiled views, and framework runtime files.
- `storage/logs/` stores Laravel logs.

For public uploads, make sure the storage link exists:

```bash
php artisan storage:link
```

### `tests/`

Contains automated tests for the Laravel application. Use `php artisan test` to run them.

### `vendor/`

Contains Composer-installed PHP packages. This folder is generated by `composer install` and should not be edited manually.

### `node_modules/`

Contains npm-installed frontend packages. This folder is generated by `npm install` and should not be edited manually.

## Styling Notes

The project uses Tailwind CSS through Vite. The global font setup is:

- **Primary/headings:** Merriweather
- **Secondary/body/forms:** Inter

All project CSS should be imported through `resources/css/app.css`. Avoid adding new CSS files in `public/css`; keep source styles under `resources/css/` so Vite can compile them.

## Security Notes

- Do not commit `.env`.
- Keep real database, mail, Google OAuth, and API credentials only in local or server environment variables.
- Use `.env.example` only as a safe template.
- Review admin routes and middleware before production deployment to ensure sensitive admin pages are protected.

## Project Summary

Tulong Kabataan is built to make community support more organized and transparent. It connects donors, volunteers, campaign creators, and administrators through one Laravel application with dedicated tools for campaigns, in-kind donations, events, verification, reporting, and impact tracking.
