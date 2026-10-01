# System Architecture

## Architecture Diagram

```mermaid
graph TD
    Client[Browser Client - HTML/Blade + Alpine.js + TailwindCSS]
    
    subgraph Laravel Framework Core
        Routes[Routes / routes/web.php]
        AuthMW[Auth / Admin Middleware]
        Controllers[HTTP Controllers]
        Requests[Form Requests Validation]
        Services[Services - CloudinaryService]
        Models[Eloquent Models]
    end

    subgraph Persistence & External Services
        DB[(SQLite / Database)]
        Cloudinary[Cloudinary CDN Storage]
        EmailService[Resend / Mail Service]
    end

    Client -->|HTTP GET/POST/PUT/DELETE| Routes
    Routes --> AuthMW
    AuthMW --> Controllers
    Controllers -->|Validates Input| Requests
    Controllers -->|Queries / Persists| Models
    Controllers -->|Uploads Receipts / Attachments| Services
    Services --> Cloudinary
    Controllers -->|Sends OTP Emails| EmailService
    Models --> DB
    Controllers -->|Renders Blade Components| Client
```

## Data Flow

1. **User Interaction**: User interacts with Blade views enhanced by Alpine.js modals, reactive dropdowns, and Chart.js graphics.
2. **Request Dispatching**: Form requests (POST/PUT/DELETE) or GET pages are sent to `routes/web.php`.
3. **Middleware & Authentication**: `auth` or `AdminMiddleware` verifies session state and role permissions before invoking Controller actions.
4. **Validation**: Specialized `FormRequest` classes (e.g. `ExpenseStoreRequest`, `ProfileUpdateRequest`) validate and sanitize incoming inputs.
5. **Business & Storage Logic**:
   - `ExpenseController` / `IncomeController` interact with `Expense` & `Income` Eloquent models.
   - Files uploaded (receipts, avatars, support attachments) pass through `CloudinaryService` to generate CDN URLs.
   - OTP emails pass through `PasswordResetOtp` Mailable.
6. **Response / View Rendering**: Controllers render Blade layouts (`<x-app-layout>`) passing structured collections to component templates.

## Auth Flow

```mermaid
sequenceDiagram
    participant User
    participant WebRoutes as routes/web.php
    participant AuthCtrl as AuthController
    participant DB as Database
    participant Mail as Resend Mailer

    %% Register / Login Flow
    User->>WebRoutes: POST /login (LoginRequest)
    WebRoutes->>AuthCtrl: login()
    AuthCtrl->>DB: Verify credentials (Auth::attempt)
    DB-->>AuthCtrl: Validated User
    AuthCtrl-->>User: Redirect to /dashboard (Authenticated Session)

    %% Forgot Password OTP Flow
    User->>WebRoutes: POST /forgot-password
    WebRoutes->>AuthCtrl: sendOtp()
    AuthCtrl->>DB: Store 6-digit OTP & expiration in session
    AuthCtrl->>Mail: Send PasswordResetOtp Email
    Mail-->>User: Delivers OTP Email
    User->>WebRoutes: POST /verify-otp
    WebRoutes->>AuthCtrl: verifyOtp()
    AuthCtrl-->>User: Marks OTP verified in session
    User->>WebRoutes: POST /reset-password-otp
    WebRoutes->>AuthCtrl: resetPasswordOtp()
    AuthCtrl->>DB: Update password hash
    AuthCtrl-->>User: Redirect to /login with success
```

## Folder Responsibility Map

| Folder | Responsibility |
| --- | --- |
| `app/Http/Controllers/` | Handlers for HTTP requests, business logic orchestration, and response compilation |
| `app/Http/Requests/` | Input validation, authorization rules, and data formatting for forms |
| `app/Http/Middleware/` | Guards routes (e.g. `AdminMiddleware` checking `is_admin` flag) |
| `app/Models/` | Eloquent ORM entity definitions, relationships, scopes, and casts |
| `app/Services/` | External API integrations (e.g., Cloudinary image upload/delete service) |
| `app/Mail/` | Transactional email definitions and Blade template pairings |
| `app/Policies/` | Fine-grained resource authorization policies (e.g., `ExpensePolicy`) |
| `database/migrations/` | Database table structure schema migrations |
| `resources/views/` | Blade templates, pages, layouts, and reusable UI components |
| `routes/web.php` | Routing hierarchy, route parameter matching, and middleware assignment |
