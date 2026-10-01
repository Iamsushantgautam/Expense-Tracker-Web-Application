# Coding Conventions & Project Rules

## Architectural & Coding Conventions

1. **Architecture & Controllers**
   - Controllers MUST handle HTTP orchestrations and return views or JSON responses.
   - Controllers MUST NOT contain raw SQL queries unless unifying distinct tables via subqueries (e.g. `TransactionController`).
   - Use `FormRequest` classes in `app/Http/Requests/` for all form input validations. NEVER place inline validation arrays inside Controllers if a FormRequest can be used.

2. **Form Submissions & Redirects**
   - All store, update, and delete actions triggered from multi-page partial modals MUST use `return redirect()->back()->with('success', '...');` instead of hardcoded route redirects so modal submissions from `/transactions` or `/dashboard` keep the user on their active page.

3. **Blade Component & View Patterns**
   - UI views MUST extend `<x-app-layout title="..." active="...">`.
   - UI components MUST reside in `resources/views/components/` and use `<x-component-name />` syntax.
   - Shared modals MUST be extracted into `resources/views/{entity}/partials/modals.blade.php`.
   - Modal partials MUST support configurable Alpine variable names (e.g. `$addModalVar ?? 'addModalOpen'`) so multiple modals (Expense & Income) can coexist in unified views without variable naming collisions.

4. **Alpine.js & Frontend Logic**
   - Use Alpine.js for lightweight frontend reactivity (modals, dropdowns, event dispatching via `$dispatch('open-edit-expense', payload)`).
   - Component state logic MUST be scoped to `x-data="{ ... }"`.
   - Do NOT use plain vanilla global script listeners when Alpine window listeners (`@open-edit-expense.window="..."`) can handle the event.

5. **Naming Conventions**
   - **PHP Classes**: PascalCase (`ExpenseController`, `CloudinaryService`).
   - **Database Tables**: Plural snake_case (`expenses`, `support_messages`).
   - **Foreign Keys**: Entity singular + `_id` (`user_id`, `category_id`).
   - **Blade Files**: Lowercase kebab-case (`index.blade.php`, `transaction-card.blade.php`).
   - **Routes**: Plural lowercase kebab-case (`expenses.index`, `api.dashboard.chart`).

---

## DOs and DONTs

### DO:
- ✅ Always authorize actions using Laravel Policies or `Gate::authorize()` / `auth()->id() === $model->user_id`.
- ✅ Always use Tailwind utility classes and flat aesthetic design tokens consistent with the application theme.
- ✅ Always pass `$expenseCategories` and `$incomeCategories` when embedding partial modals in multi-entity views.
- ✅ Always clear configuration/views after route or Blade changes (`php artisan optimize:clear`).

### DONT:
- ❌ DON'T write inline CSS styles unless dynamic hex background colors are computed (e.g., category badges).
- ❌ DON'T use global `$_POST` or `$_GET` arrays. Use Laravel `$request->input()` or `$request->validated()`.
- ❌ DON'T hardcode Cloudinary image processing logic in Controllers. Use `App\Services\CloudinaryService`.
- ❌ DON'T delete unit/feature tests or remove policy checks to bypass permissions.

---

## Step-by-Step Rule: How to Add a New Feature

1. **Step 1: Database Migration & Model**
   - Create migration: `php artisan make:migration create_feature_table`
   - Create Eloquent Model in `app/Models/Feature.php` with `$fillable` fields, `$casts`, and relationship methods (`user()`, etc.).

2. **Step 2: Form Request Validation**
   - Create Request: `php artisan make:request FeatureStoreRequest`
   - Implement `rules()` and `authorize()` methods.

3. **Step 3: Controller & Routes**
   - Create Controller in `app/Http/Controllers/FeatureController.php`.
   - Implement standard Resource methods (`index`, `store`, `update`, `destroy`).
   - Use `redirect()->back()` for form responses.
   - Register routes in `routes/web.php` inside `Route::middleware('auth')`.

4. **Step 4: Views & Partial Modals**
   - Create views in `resources/views/feature/index.blade.php`.
   - Create partial modals in `resources/views/feature/partials/modals.blade.php` using `$addModalVar` / `$editModalVar` parameters.
   - Register components in `resources/views/components/` if reusable.

5. **Step 5: Documentation Update**
   - Update `/docs/API_DOCS.md`, `/docs/DATABASE.md`, `/docs/COMPONENTS.md`, `/docs/FOLDER_STRUCTURE.md`, `/docs/LLM_CONTEXT.md`, and `/docs/CHANGELOG.md`.
