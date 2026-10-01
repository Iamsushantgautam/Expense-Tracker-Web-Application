# API & Route Reference

All routes are web-authenticated routes configured in `routes/web.php`.

## Guest & Authentication Routes

| Method | Path | Purpose | Input | Output / Redirect |
| --- | --- | --- | --- | --- |
| `GET` | `/` | Root Route Redirect | None | Redirects to `/dashboard` if auth, else `/login` |
| `GET` | `/login` | Show Login View | None | `auth.login` view |
| `POST` | `/login` | Process Login | `email`, `password`, `remember` | Redirects to `/dashboard` |
| `GET` | `/register` | Show Register View | None | `auth.register` view |
| `POST` | `/register` | Register User | `name`, `username`, `email`, `password`, `password_confirmation` | Redirects to `/dashboard` |
| `GET` | `/forgot-password` | Enter Email for OTP | None | `auth.forgot-password` view |
| `POST` | `/forgot-password` | Send OTP Email | `email` | Redirects to `/verify-otp` |
| `GET` | `/verify-otp` | Show OTP Verification | None | `auth.verify-otp` view |
| `POST` | `/verify-otp` | Submit OTP Code | `otp` | Redirects to `/reset-password-otp` |
| `GET` | `/reset-password-otp` | Show New Password Form | None | `auth.reset-password-otp` view |
| `POST` | `/reset-password-otp` | Submit New Password | `password`, `password_confirmation` | Redirects to `/login` with success |
| `POST` | `/logout` | Destroy Session | None | Redirects to `/login` |

## Authenticated Application Routes

| Method | Path | Route Name | Purpose | Input | Output / Redirect |
| --- | --- | --- | --- | --- | --- |
| `GET` | `/dashboard` | `dashboard` | User Dashboard | None | `dashboard.index` view with stats & charts |
| `GET` | `/api/dashboard/chart-data` | `api.dashboard.chart` | Chart.js Endpoint | Query: `range` (`7d`, `30d`, `6m`, `1y`) | JSON: `{ labels: [], data: [] }` |
| `GET` | `/expenses/export` | `expenses.export` | Export Expense CSV | Query filters: `category`, `date_from`, `date_to` | Streamed CSV File Download |
| `GET` | `/expenses` | `expenses.index` | Expenses Listing | Query: `search`, `category`, `date_from`, `date_to`, `sort`, `direction` | `expenses.index` view |
| `POST` | `/expenses` | `expenses.store` | Create Expense | `amount`, `title`, `category_id`, `payment_method`, `date`, `notes`, `receipt` | Redirect back with success flash |
| `GET` | `/expenses/{expense}` | `expenses.show` | Expense Details | URL param | `expenses.show` view |
| `PUT` | `/expenses/{expense}` | `expenses.update` | Update Expense | `amount`, `title`, `category_id`, `payment_method`, `date`, `notes`, `receipt` | Redirect back with success flash |
| `DELETE` | `/expenses/{expense}` | `expenses.destroy` | Delete Expense | URL param | Redirect back with success flash |
| `GET` | `/incomes` | `incomes.index` | Incomes Listing | Query: `search`, `source`, `date_from`, `date_to`, `sort`, `direction` | `incomes.index` view |
| `POST` | `/incomes` | `incomes.store` | Create Income | `amount`, `title`, `source`, `payment_method`, `date`, `notes` | Redirect back with success flash |
| `PUT` | `/incomes/{income}` | `incomes.update` | Update Income | `amount`, `title`, `source`, `payment_method`, `date`, `notes` | Redirect back with success flash |
| `DELETE` | `/incomes/{income}` | `incomes.destroy` | Delete Income | URL param | Redirect back with success flash |
| `GET` | `/transactions` | `transactions.index` | Unified Timeline | Query: `type` (`all`,`income`,`expense`), `search`, `date_from`, `date_to`, `sort` | `transactions.index` view |
| `GET` | `/categories` | `categories.index` | Manage Categories | None | `categories.index` view |
| `POST` | `/categories` | `categories.store` | Create Category | `name`, `type` (`expense`/`income`), `color`, `icon` | Redirect back with success flash |
| `PUT` | `/categories/{category}` | `categories.update` | Edit Category | `name`, `color`, `icon` | Redirect back with success flash |
| `DELETE` | `/categories/{category}` | `categories.destroy` | Delete Category | URL param | Redirect back with success flash |
| `GET` | `/reports` | `reports.index` | Financial Reports | Query: `year`, `month` | `reports.index` view |
| `GET` | `/profile` | `profile.edit` | Profile & Preferences | None | `profile.edit` view |
| `PUT` | `/profile` | `profile.update` | Update Settings | `name`, `username`, `email`, `profile_pic`, `monthly_budget`, `budget_warn_limit`, `theme_color`, `dark_mode` | Redirect back with success flash |
| `PUT` | `/profile/password` | `profile.password` | Change Password | `current_password`, `password`, `password_confirmation` | Redirect back with success flash |
| `GET` | `/support` | `support.index` | Support Center | None | `support.index` view |
| `POST` | `/support` | `support.store` | Submit Ticket | `subject`, `message`, `attachments[]` | Redirect back with ticket number |

## Admin Panel Routes (Guarded by `AdminMiddleware`)

| Method | Path | Route Name | Purpose | Input | Output / Redirect |
| --- | --- | --- | --- | --- | --- |
| `GET` | `/admin` | `admin.index` | Admin Dashboard | None | `admin.index` view |
| `GET` | `/admin/support` | `admin.support` | View Support Tickets | Query filters | `admin.support` view |
| `PUT` | `/admin/support/{supportMessage}` | `admin.support.update` | Update Ticket Status | `status` (`open`, `in_progress`, `resolved`, `closed`) | Redirect back |
| `GET` | `/admin/users` | `admin.users` | Registered Users List | Query search | `admin.users` view |
