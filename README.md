# Tulong Kabataan

Tulong Kabataan is a web-based donation, campaign, volunteer, and community support platform. It helps users browse active campaigns, donate through a QR/payment proof flow, track donation activity, register for volunteer events, submit in-kind donations, and view transparency updates about how donations are used. 

Live website: **https://tkb-staging.tulongkabataanbicol.com/**

## Project Overview

The system is built for youth-focused community support and relief initiatives. It provides public pages for campaigns, events, in-kind donations, impact reports, and policy information, while also giving registered users a dashboard for managing their profile, campaigns, donations, event registrations, verification status, and notifications.

An administrator area is included for reviewing accounts, managing campaigns, checking donation proofs, publishing impact reports, managing events, maintaining drop-off points, and controlling site settings.

## Purpose of the System

The purpose of Tulong Kabataan is to make community support easier to organize and easier to verify. The platform helps:

- Donors find campaigns and submit donations with payment proof.
- Campaign creators publish fundraising campaigns and track donation progress.
- Volunteers register for community events.
- Users submit and track in-kind donations.
- Administrators review records and publish transparency reports.
- Visitors understand platform policies, contact channels, and donation usage.

## Key Features

- Campaign listing and campaign detail pages
- QR-based donation flow with reference number and proof upload
- Campaign progress tracking, donor count, and campaign updates
- User registration, login, Google authentication, email verification, and password reset
- User profile and dashboard pages
- Identity verification workflow
- In-kind donation submission and tracking
- Drop-off point management
- Volunteer event browsing and registration
- Notifications for campaign, donation, event, and verification activity
- Chatbot assistance for user-side platform questions
- Transparency and impact report pages
- Legal and policy pages
- Administrator dashboard and reporting tools

## User-Side Features

- Browse public campaigns
- View campaign details, goals, schedules, QR code, and updates
- Donate to a campaign using GCash reference details and proof upload
- Create campaigns after logging in
- Track owned campaigns and donation activity
- Add campaign updates
- Register and verify an account
- Update profile details, password, and profile photo
- Submit identity verification documents
- Browse volunteer events and register for roles
- View event registration history
- Submit in-kind donations
- Track in-kind donation status
- View impact reports and in-kind transparency information
- Receive notifications
- Use the chatbot assistant for platform guidance
- Access About, Contact, Privacy Policy, Terms of Service, Cookie Policy, and Sitemap pages

## Admin-Side Features

- Admin login and dashboard
- Account verification review and decision handling
- Campaign monitoring and campaign detail review
- Manual donation request approval or rejection
- Campaign statistics, monthly funds, and PDF export
- In-kind donation status management
- Drop-off point creation, update, activation, and deletion
- Impact report creation
- Event creation and volunteer registration management
- Volunteer statistics and live event data
- DNC records management
- Site settings for maintenance mode, announcements, registration, Google login, chatbot visibility, and public page access
- User account activation, suspension, and deletion

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Laravel Blade, Vite, JavaScript, React components for maps
- **Styling:** Tailwind CSS 4 and modular CSS files
- **Database:** MongoDB using `mongodb/laravel-mongodb`
- **Storage:** Cloudflare R2 through Laravel's S3-compatible filesystem driver
- **Authentication:** Laravel authentication flow, sessions, email verification, Laravel Socialite for Google login
- **Chatbot:** Groq API configuration
- **PDF/Reports:** Dompdf
- **Maps:** Leaflet, React Leaflet
- **Build Tools:** Composer, npm, Vite

## Project Folder Structure

```text
Tulong-Kabataan-main/
|-- app/                          # Laravel application code
|   |-- Http/Controllers/          # Login, profile, campaign, event, in-kind, chatbot, and admin controllers
|   |-- Jobs/                      # Queued jobs for scheduled campaign and event work
|   |-- Livewire/                  # Livewire components
|   |-- Mail/                      # Mail classes
|   |-- Models/                    # MongoDB Eloquent models
|   |-- Notifications/             # Laravel notifications
|   `-- Services/                  # Chatbot and Cloudflare R2 storage services
|-- bootstrap/                     # Laravel bootstrap and route loading configuration
|-- config/                        # App, database, mail, queue, filesystem, R2, and service config
|-- database/                      # Migrations, factories, and seeders
|-- public/                        # Public entry point, images, and page-level JavaScript
|-- resources/
|   |-- css/                       # Tailwind entry file and modular CSS
|   |-- js/                        # Vite-managed JavaScript and React map components
|   `-- views/                     # Blade templates grouped by feature
|-- routes/                        # Route files split by feature area
|-- scripts/                       # Utility scripts, including image conversion
|-- storage/                       # Logs, framework cache, and runtime files
|-- tests/                         # Laravel tests
|-- composer.json                  # PHP dependencies and scripts
|-- package.json                   # Frontend dependencies and scripts
|-- vite.config.js                 # Vite configuration
`-- vercel.json                    # Deployment configuration
```

## Installation Steps

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- MongoDB database
- PHP MongoDB extension enabled
- Cloudflare R2 account if file uploads will use R2

### Setup

1. Clone the repository.

```bash
git clone <repository-url>
cd Tulong-Kabataan-main
```

2. Install PHP dependencies.

```bash
composer install
```

3. Install frontend dependencies.

```bash
npm install
```

4. Create the local environment file.

```bash
copy .env.example .env
```

For macOS or Linux:

```bash
cp .env.example .env
```

5. Generate the application key.

```bash
php artisan key:generate
```

6. Configure `.env` with database, mail, storage, Google login, and chatbot values as needed.

7. Run migrations if your environment uses the migration-backed setup.

```bash
php artisan migrate
```

8. Build frontend assets.

```bash
npm run build
```

## Environment Variables Example

```env
APP_NAME="Tulong Kabataan"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mongodb
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DB_NAME=tulong_kabataan

SESSION_DRIVER=file
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=r2

MAIL_MAILER=smtp
MAIL_HOST=your-mail-host
MAIL_PORT=587
MAIL_USERNAME=your-mail-username
MAIL_PASSWORD=your-mail-password
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="${APP_NAME}"

R2_ACCOUNT_ID=
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET_NAME=
R2_ENDPOINT=
R2_PUBLIC_URL=
R2_REGION=auto
R2_CONVERT_IMAGES_TO_WEBP=true

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback

GROQ_API_KEY=
GROQ_MODEL=llama-3.3-70b-versatile

VITE_APP_NAME="${APP_NAME}"
VITE_MAP_TILE_URL=
VITE_MAP_TILE_ATTRIBUTION=
VITE_MAPTILER_API_KEY=
VITE_NOMINATIM_BASE_URL=
```

Do not commit real `.env` credentials.

## How to Run Locally

Start the Laravel development server:

```bash
php artisan serve
```

Start Vite in a separate terminal during frontend development:

```bash
npm run dev
```

Run the queue worker if testing queued jobs and notifications:

```bash
php artisan queue:listen --tries=1
```

The application will usually be available at:

```text
http://127.0.0.1:8000
```

You can also use the Composer development script:

```bash
composer run dev
```

## Payment Setup Note

The current donation flow uses a QR/payment proof process. Campaign creators upload a GCash QR code and GCash number when creating a campaign. Donors submit the donation amount, reference number, and proof image. Submitted donations are stored for review and transparency tracking.

No Xendit or other automated payment gateway is currently wired into the codebase. If a payment provider is added later, place the provider credentials in `.env`, add server-side webhook validation, and update the donation status flow to rely on verified payment events.

## File Upload and Storage Setup Note

File uploads are handled through `App\Services\Storage\R2StorageService` and Cloudflare R2. The app stores object keys in the database and generates public URLs from the configured R2 public URL.

Common upload folders are configured in `config/r2.php`, including:

- User profile photos and verification documents
- Campaign featured images, campaign images, and campaign QR codes
- Donation proof images
- Event banners
- Impact report photos

Set `FILESYSTEM_DISK=r2` and configure the `R2_*` variables in `.env`. Image uploads can be converted to WebP using `scripts/convert-image-to-webp.mjs`.

For local-only storage, update `FILESYSTEM_DISK` and file URL handling carefully before testing uploads.

## API Overview

Most routes return Blade pages or JSON responses from Laravel controllers. Route files are split by feature in the `routes/` directory.

| Area | Main Routes | Description |
| --- | --- | --- |
| Public pages | `/`, `/about-us`, `/privacy-policy`, `/terms-of-service`, `/cookie-policy`, `/contact-us`, `/sitemap` | Landing, information, and policy pages |
| Authentication | `/login`, `/register`, `/loginaccount`, `/registeraccount`, `/auth/google`, `/email/verify` | Login, registration, Google OAuth, email verification, and password reset |
| Campaigns | `/campaignpage`, `/campaignview/{id}`, `/campaign/create`, `/campaigns`, `/donations/store` | Campaign browsing, campaign creation, and donation submission |
| Profile | `/profile`, `/profile/dashboard`, `/profile/event`, `/profile/inkind`, `/verifypage` | User profile, dashboard, verification, campaign owner tools, event history, and in-kind history |
| Events | `/eventpage`, `/eventview/{event_id}`, `/event-register/{event}`, `/submit-registration`, `/events/updates` | Event browsing, event details, volunteer registration, and live updates |
| In-kind donations | `/inkindpage`, `/inkind-donate`, `/my-donations`, `/in-kind-tracking`, `/api/impact-reports/{id}` | In-kind donation submission, tracking, stats, and public impact report data |
| Notifications | `/notifications`, `/notifications/all`, `/notifications/mark-all-read`, `/notifications/{notification}/read` | User notification display and status updates |
| Chatbot | `/chatbot/message` | JSON endpoint for chatbot replies |
| Admin | `/administrator/*` | Admin dashboard, accounts, campaigns, donations, events, DNC records, settings, and reports |

To view all registered routes locally:

```bash
php artisan route:list
```

## Screenshots

Add screenshots after deployment or local UI testing.

### Landing Page

```text
screenshots/landing-page.png
```

### Campaign Listing

```text
screenshots/campaign-listing.png
```

### Campaign Details and Donation Flow

```text
screenshots/campaign-details.png
```

### User Dashboard

```text
screenshots/user-dashboard.png
```

### In-Kind Donation Tracking

```text
screenshots/inkind-tracking.png
```

### Admin Dashboard

```text
screenshots/admin-dashboard.png
```

## Deployment Instructions

1. Prepare the production environment with PHP 8.2+, Composer, Node.js, npm, MongoDB access, and required PHP extensions.

2. Install dependencies.

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

3. Create and configure the production `.env` file.

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_CONNECTION=mongodb
FILESYSTEM_DISK=r2
```

4. Generate the application key if it has not been generated yet.

```bash
php artisan key:generate
```

5. Run migrations if required by the target database setup.

```bash
php artisan migrate --force
```

6. Cache production configuration.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Make sure the web server points to the `public/` directory.

8. Start or configure the queue worker if using queued jobs.

```bash
php artisan queue:work --tries=3
```

9. Configure scheduled tasks if recurring campaign or event reminder jobs are used.

```bash
php artisan schedule:run
```

In production, run the scheduler through the server's task scheduler or cron.

## Future Improvements

- Add an automated payment gateway with webhook verification
- Add stricter role-based access control for admin modules
- Improve donation receipt generation
- Add more automated tests for donation, campaign, verification, and admin workflows
- Add a dedicated screenshot gallery for documentation
- Add audit logs for admin actions
- Improve campaign search and filtering
- Add dashboard export options for more report types
- Add more detailed transparency analytics for donors

## Contributors

- Tulong Kabataan development team

Contributors can add their names here as the project is maintained.

## License

This project is currently documented as an academic/community capstone project. Add a specific license before public distribution or reuse.
