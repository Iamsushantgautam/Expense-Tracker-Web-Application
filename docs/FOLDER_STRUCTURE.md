# Folder Structure Map

```text
wit-expense-tracker/
├── app/                                  # Core application PHP code
│   ├── Http/
│   │   ├── Controllers/                  # Request handler controllers
│   │   │   ├── AdminController.php       # Admin management (user list, support tickets)
│   │   │   ├── AuthController.php        # Login, registration, logout, OTP reset password
│   │   │   ├── CategoryController.php    # Custom category CRUD (expense/income)
│   │   │   ├── Controller.php            # Base controller class
│   │   │   ├── DashboardController.php   # Dashboard stats, charts API, budget status
│   │   │   ├── ExpenseController.php     # Expense CRUD & CSV export
│   │   │   ├── IncomeController.php      # Income record CRUD
│   │   │   ├── ProfileController.php     # User profile, theme, budget & password updates
│   │   │   ├── ReportController.php      # Analytics reports & spending charts
│   │   │   ├── SupportController.php     # Support tickets submission & list
│   │   │   └── TransactionController.php # Unified timeline combining incomes & expenses
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php       # Guards admin routes checking $user->is_admin
│   │   └── Requests/                     # Form request validation classes
│   │       ├── ExpenseStoreRequest.php   # Validates new expense input
│   │       ├── ExpenseUpdateRequest.php # Validates updated expense input
│   │       ├── IncomeStoreRequest.php    # Validates new income input
│   │       ├── IncomeUpdateRequest.php  # Validates updated income input
│   │       ├── LoginRequest.php         # Validates login credentials
│   │       ├── PasswordUpdateRequest.php# Validates password change
│   │       ├── ProfileUpdateRequest.php # Validates profile settings update
│   │       ├── RegisterRequest.php      # Validates user registration fields
│   │       └── SupportMessageRequest.php# Validates support ticket submission
│   ├── Mail/
│   │   └── PasswordResetOtp.php          # Mailable for password reset OTP emails
│   ├── Models/                           # Eloquent ORM Models
│   │   ├── Category.php                  # Category model (income & expense types)
│   │   ├── Expense.php                   # Expense model
│   │   ├── Income.php                    # Income model
│   │   ├── SupportMessage.php            # Support ticket model with auto-generated TKT-XXXX
│   │   └── User.php                      # User model with budget & auth credentials
│   ├── Policies/
│   │   └── ExpensePolicy.php             # Authorization rules for expenses
│   ├── Providers/                        # Service Providers
│   └── Services/
│       └── CloudinaryService.php         # Media file uploader & Cloudinary integration
├── bootstrap/                            # Laravel app bootstrapping & configuration
├── config/                               # Framework configuration files (app, auth, database, etc.)
├── database/
│   ├── factories/                        # Database factories for testing
│   ├── migrations/                       # Database schema migration files
│   └── seeders/                          # Database seeders (default categories, demo user)
├── docs/                                 # Project documentation repository
├── public/                               # Web root directory (index.php, compiled assets)
├── resources/
│   ├── css/                              # TailwindCSS styles
│   ├── js/                               # Frontend JS entries
│   └── views/                            # Blade UI views
│       ├── admin/                        # Admin panel views (support list, user table)
│       ├── auth/                         # Login, Register, Forgot Password OTP views
│       ├── categories/                   # Category management views & partial modals
│       ├── components/                   # Reusable Blade UI components (input, select, stat-card, etc.)
│       ├── dashboard/                    # Main dashboard overview view
│       ├── emails/                       # Email HTML Blade templates
│       ├── expenses/                     # Expense management views & partial modals
│       ├── incomes/                      # Income management views & partial modals
│       ├── layouts/                      # App master layout & guest layout
│       ├── profile/                      # Profile settings view
│       ├── reports/                      # Visual analytics and reports view
│       ├── support/                      # Support ticket submission view
│       └── transactions/                 # Unified transaction timeline view
├── routes/
│   ├── console.php                       # Artisan console commands
│   └── web.php                           # Application HTTP web routes
├── storage/                              # Application logs, sessions, cache & uploads
├── Dockerfile                            # Docker container build script
├── render.yaml                           # Cloud deployment configuration for Render
├── composer.json                         # PHP Composer dependency specifications
├── package.json                          # Vite & Node frontend dependency specifications
└── vite.config.js                        # Vite bundler configuration
```
