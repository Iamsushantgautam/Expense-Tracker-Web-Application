# 💸 Wit Expense Tracker

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.0-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Vite](https://img.shields.io/badge/Vite-v7.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](LICENSE)

A modern, full-featured financial management web application built with **Laravel 12**, **Tailwind CSS 4**, **Alpine.js**, and **Chart.js**. Effortlessly track income and expenses, analyze monthly cash flow, configure custom budgets, export transactions to CSV, upload receipt attachments via **Cloudinary**, and personalize your user dashboard with dynamic color themes and dark mode.

---

## ✨ Features

- 📊 **Interactive Analytics Dashboard**: Real-time cash flow overview, monthly spending summaries, category allocation charts powered by **Chart.js**, and budget warning thresholds.
- 💵 **Expense & Income Management**: Full CRUD capabilities with category tagging, payment method tracking, reference numbers, dynamic receipts attachment, and pagination.
- 📑 **Unified Transactions Feed**: View all incomes and expenses in a centralized, searchable stream with filter controls by type, category, date range, and quick action options.
- 🏷️ **Custom Categories**: Define custom income and expense categories with personalized icons and color badges.
- 📈 **Financial Reports & CSV Export**: Generate comprehensive monthly & yearly spending insights and export expense history to `.csv` format.
- 🎨 **Dynamic Theme System**: 6 custom theme presets (*Indigo*, *Emerald*, *Rose*, *Amber*, *Purple*, *Cyan*) with seamless Light/Dark mode toggling.
- ☁️ **Cloudinary Image Integration**: Automated file uploads for profile avatars and receipt documents organized per user directory (`WitExpenseTracker/{username}/...`).
- 💬 **Support & Help Desk**: Integrated ticket system allowing users to submit queries and feedback directly to administrators.
- 🛡️ **Admin Control Panel**: Dedicated management center for system administrators to view user analytics, manage registered accounts, and respond to support tickets.
- 🔒 **Security & Authentication**: Built-in user authentication, password update workflows, and role-based middleware (`AdminMiddleware`).

---

## 🛠️ Tech Stack

| Domain | Technology |
|---|---|
| **Backend** | [Laravel 12](https://laravel.com), PHP 8.2+ |
| **Frontend** | Blade Templates, [Tailwind CSS 4](https://tailwindcss.com), [Alpine.js 3](https://alpinejs.dev) |
| **Charts & Visuals** | [Chart.js 4](https://www.chartjs.org/) |
| **Build Tooling** | [Vite 7](https://vitejs.dev/) |
| **Database** | SQLite (Default) / MySQL / PostgreSQL |
| **Media Management** | Cloudinary REST API / Laravel Storage |

---

## 🚀 Quick Start Guide

### Prerequisites

Ensure you have the following installed on your local environment:
- **PHP** `>= 8.2`
- **Composer** `>= 2.x`
- **Node.js** `>= 18.x` & **npm**
- **SQLite** (or MySQL)

### Step 1: Clone the Repository

```bash
git clone https://github.com/your-username/expense-tracker.git
cd expense-tracker
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration

Copy the example `.env` file and generate an application encryption key:

```bash
cp .env.example .env
php artisan key:generate
```

Ensure SQLite database file exists (if using SQLite default):

```bash
touch database/database.sqlite
```

### Step 4: Run Migrations & Database Seeders

```bash
php artisan migrate --seed
```

### Step 5: Compile Frontend Assets

```bash
# Production Build
npm run build

# OR Development Mode (Hot Reloading)
npm run dev
```

### Step 6: Start Local Development Server

```bash
php artisan serve
```

The application will be available at: `http://127.0.0.1:8000`

---

## ⚙️ Environment Variables (`.env`)

Configure the following key settings in your `.env` file:

```ini
APP_NAME="Wit Expense Tracker"
APP_ENV=local
APP_KEY=base64:...
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=sqlite

# Cloudinary Setup (Optional - for image uploads)
CLOUDINARY_URL=cloudinary://API_KEY:API_SECRET@CLOUD_NAME
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
```

---

## 📂 Project Structure

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Dashboard, Expense, Income, Admin, Profile, Support Controllers
│   │   └── Middleware/      # AdminMiddleware for route protection
│   ├── Models/              # User, Expense, Income, Category, SupportMessage
│   └── Services/            # CloudinaryService for file uploads
├── database/
│   ├── migrations/          # Schema definitions
│   └── seeders/             # Initial category & admin user data
├── resources/
│   ├── views/               # Blade templates & components
│   └── css/                 # Tailwind CSS styles & dynamic themes
├── routes/
│   └── web.php              # Web application route declarations
└── public/                  # Compiled Vite assets & public uploads
```

## 🐳 Render Deployment (Docker)

This application includes a multi-stage `Dockerfile`, entrypoint runtime scripts, Nginx/Supervisor configs, and a `render.yaml` Blueprint spec ready for one-click deployment on [Render](https://render.com).

### Option A: Deploy via Render Blueprint (Recommended)

1. Push your repository to GitHub or GitLab.
2. Sign in to [Render Dashboard](https://dashboard.render.com).
3. Click **New +** -> **Blueprint**.
4. Connect your repository. Render will automatically detect `render.yaml` and configure the Web Service with Docker.
5. Set environment variables (such as `APP_URL` and Cloudinary credentials) in the Render dashboard and click **Apply**.

### Option B: Deploy as a Render Web Service (Manual)

1. Go to Render Dashboard -> **New +** -> **Web Service**.
2. Select **Build and deploy from a Git repository**.
3. Choose **Docker** as the Runtime.
4. Set the **Environment Variables** in Render:
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: Generate via `php artisan key:generate --show`
   - `APP_URL`: `https://your-app-name.onrender.com`
   - `DB_CONNECTION`: `sqlite` (or `mysql` if using a external MySQL DB)
   - `RUN_MIGRATIONS`: `true`
   - *(Optional)* `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`
5. Click **Deploy Web Service**. Render will build the Docker container and route traffic automatically on port `$PORT`.

### Local Docker Testing

To build and run the production Docker image locally:

```bash
# Build the Docker image
docker build -t expense-tracker .

# Run the container locally on port 8000
docker run -p 8000:10000 -e APP_KEY="base64:your_app_key_here" expense-tracker
```

---

## 📄 License

This project is open-source software licensed under the [MIT License](LICENSE).


