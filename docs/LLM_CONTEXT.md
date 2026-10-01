# Master LLM Context: WitExpenseTracker

> SINGLE SOURCE OF TRUTH FOR AI ASSISTANTS & DEVELOPERS.

---

## 1. Project Overview & Tech Stack
WitExpenseTracker is a modern personal finance application built with **Laravel 12 (PHP 8.2+)**, **Blade Components**, **Alpine.js v3**, **TailwindCSS v4**, and **Chart.js v4**. It features income/expense logging, budget warnings, custom categories, CSV export, Cloudinary receipt attachments, OTP email password reset via Resend, and an admin management dashboard.

---

## 2. Core Architecture & Folder Structure

```text
app/
├── Http/Controllers/ (AdminController, AuthController, CategoryController, DashboardController, ExpenseController, IncomeController, ProfileController, ReportController, SupportController, TransactionController)
├── Http/Requests/ (Validation classes for forms)
├── Http/Middleware/ (AdminMiddleware)
├── Models/ (User, Expense, Income, Category, SupportMessage)
├── Policies/ (ExpensePolicy)
└── Services/ (CloudinaryService)
resources/views/
├── components/ (Reusable UI components: input, select, button, stat-card, transaction-card, empty-state, filter-modal, search-bar, spending-chart, topbar, sidebar, toast)
├── expenses/partials/modals.blade.php & incomes/partials/modals.blade.php (Reusable Modal Partials)
└── (dashboard, expenses, incomes, transactions, categories, reports, profile, support, admin, auth)
routes/web.php
```

---

## 3. Database Schema Overview

1. **`users`**: `id`, `name`, `username`, `email`, `password`, `profile_pic`, `monthly_budget`, `budget_warn_limit`, `theme_color`, `dark_mode`, `is_admin`, timestamps.
2. **`categories`**: `id`, `user_id` (nullable), `name`, `type` (`expense`/`income`), `slug`, `icon`, `color`, `is_default`, `status`, timestamps.
3. **`expenses`**: `id`, `user_id`, `category_id` (nullable), `title`, `amount`, `category`, `date`, `payment_method`, `notes`, `receipt_url`, `receipt_public_id`, timestamps.
4. **`incomes`**: `id`, `user_id`, `title`, `amount`, `source`, `date`, `payment_method`, `notes`, timestamps.
5. **`support_messages`**: `id`, `ticket_number` (`TKT-XXXXXXXX`), `user_id`, `name`, `email`, `subject`, `message`, `attachment_urls` (JSON), `attachment_public_ids` (JSON), `status`, timestamps.

---

## 4. Key API Endpoints & Routes Summary

- **Guest Routes**: `/login`, `/register`, `/forgot-password`, `/verify-otp`, `/reset-password-otp`
- **Dashboard & API**: `/dashboard`, `/api/dashboard/chart-data`
- **Expenses**: `/expenses` (CRUD), `/expenses/export` (CSV)
- **Incomes**: `/incomes` (CRUD)
- **Transactions**: `/transactions` (Unified timeline)
- **Categories**: `/categories` (CRUD)
- **Reports & Profile**: `/reports`, `/profile` (Update settings & password)
- **Support**: `/support` (Submit & view tickets)
- **Admin**: `/admin`, `/admin/support`, `/admin/users`

---

## 5. Coding & Integration Rules

1. **Modals Partial Pattern**: Always include modal partials using configurable state variables:
   ```blade
   @include('expenses.partials.modals', ['categories' => $expenseCategories, 'addModalVar' => 'addExpenseModalOpen', 'editModalVar' => 'editExpenseModalOpen'])
   ```
2. **Redirect Rule**: Controllers handling modal form actions MUST use `redirect()->back()->with('success', '...')`.
3. **Validation Rule**: All input validation MUST use FormRequest classes in `app/Http/Requests/`.
4. **Component Usage**: Use `<x-input>`, `<x-select>`, `<x-button>`, `<x-transaction-card>`, `<x-stat-card>`, `<x-empty-state>`.
5. **Security & Authorization**: Enforce `AdminMiddleware` for admin routes, policy checks or `$model->user_id === auth()->id()` for data operations.
