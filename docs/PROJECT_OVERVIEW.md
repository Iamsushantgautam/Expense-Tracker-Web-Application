# Project Overview: WitExpenseTracker (Laravel 12 Tech Stack)

WitExpenseTracker is a modern, full-stack personal finance and expense tracking web application. It enables users to record income payouts and expense transactions, organize spending into customizable categories, set monthly budget limits with automatic warning thresholds, analyze financial data via interactive charts and CSV exports, and submit support tickets. It features a responsive layout with dark mode support and an administrative dashboard for system management.

## Tech Stack

| Layer | Technology |
| --- | --- |
| **Backend Framework** | Laravel 12.x (PHP ^8.2) |
| **Frontend UI / Templating** | Blade Components, Alpine.js v3.14, TailwindCSS v4.0 |
| **Build System & Asset Bundling** | Vite v7.0 with `@tailwindcss/vite` & `laravel-vite-plugin` |
| **Database** | SQLite (default) / MySQL / PostgreSQL support via Eloquent ORM |
| **Authentication** | Session-based Auth with custom `AuthController` (Register, Login, OTP Email Password Reset) |
| **Media / File Storage** | Cloudinary API via custom `CloudinaryService` |
| **Charts & Visualizations** | Chart.js v4.4 |
| **Email Services** | Resend (`resent/laravel`) / SMTP / Log |
| **Hosting & Containerization** | Docker (`Dockerfile`), Render (`render.yaml`), Apache / Nginx |

## How to Run Locally

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Environment configuration
copy .env.example .env
php artisan key:generate

# 4. Run database migrations
php artisan migrate --seed

# 5. Start development servers (Vite + PHP Artisan Serve concurrently)
npm run dev
# OR run concurrently via composer:
composer run dev
```

## Environment Variables

| Variable | Description | Default / Example |
| --- | --- | --- |
| `APP_NAME` | Application Name | `WitExpenseTracker` |
| `APP_ENV` | Environment state | `local` |
| `APP_KEY` | Application encryption key | `base64:...` |
| `APP_DEBUG` | Debug mode | `true` |
| `APP_URL` | Base application URL | `http://localhost:8000` |
| `DB_CONNECTION` | Database driver | `sqlite` / `mysql` |
| `SESSION_DRIVER` | Session storage driver | `database` / `file` |
| `QUEUE_CONNECTION` | Queue driver | `database` / `sync` |
| `CLOUDINARY_URL` | Cloudinary connection string for media uploads | `cloudinary://API_KEY:SECRET@CLOUD_NAME` |
| `RESEND_API_KEY` | Resend API key for transactional emails & OTP | `re_...` |
| `MAIL_FROM_ADDRESS` | Sender email address | `hello@example.com` |
