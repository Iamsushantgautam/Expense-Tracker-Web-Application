# Reusable Blade UI Components

All components are located in `resources/views/components/` and are rendered using Blade tag syntax `<x-component-name />`.

| Component | Props | Description | Used In |
| --- | --- | --- | --- |
| `<x-input />` | `name`, `label`, `type`, `value`, `placeholder`, `icon`, `required`, `disabled` | Form input field with integrated error display and icon prefix | All modal forms, profile edit, auth views |
| `<x-select />` | `name`, `label`, `placeholder`, `required`, `selected`, `disabled` | Styled select dropdown with validation error rendering | Expense modals, Income modals |
| `<x-button />` | `type`, `variant` (`primary`/`outline`/`danger`), `size` (`sm`/`md`/`lg`), `href`, `icon` | Button or anchor link with standard Tailwind styles | Page headers, form actions |
| `<x-stat-card />` | `title`, `value`, `subtitle`, `icon`, `change`, `changeType` | KPI Stat card displaying financial totals and percentages | `dashboard/index.blade.php`, `reports/index.blade.php` |
| `<x-transaction-card />` | `transaction` | Card rendering a unified income or expense transaction with edit & delete events | `dashboard/index.blade.php`, `expenses/index.blade.php`, `incomes/index.blade.php`, `transactions/index.blade.php` |
| `<x-expense-row />` | `expense` | Table row component for tabular expense lists | Older list view tables |
| `<x-category-badge />` | `name`, `color` | Soft badge tag displaying category color and label | Category lists, transaction details |
| `<x-empty-state />` | `title`, `description`, `icon`, `actionUrl`, `actionLabel`, `actionEvent` | Clean empty state placeholder with optional action trigger | All index list views when zero items exist |
| `<x-filter-modal />` | `action`, `categories`, `sources`, `typeFilter`, `resetUrl` | Modal dialog for advanced search & date range filtering | Expense index, Income index, Transaction index |
| `<x-search-bar />` | `action`, `placeholder`, `hiddenFields` | Search input bar with hidden inputs preserving state | Page control toolbars |
| `<x-spending-chart />` | None | Interactive bar chart component fetching chart data via Alpine & Chart.js | `dashboard/index.blade.php`, `reports/index.blade.php` |
| `<x-sidebar />` | `active` | Main application navigation sidebar | Layout container (`layouts/app.blade.php`) |
| `<x-topbar />` | None | Top application header bar with notifications, profile avatar, theme switch | Layout container (`layouts/app.blade.php`) |
| `<x-alert />` | `type` (`success`/`error`/`warning`), `message` | Flash message alert block | Page layouts |
| `<x-toast />` | None | Toast notification component handling session flash messages via Alpine.js | Layout container (`layouts/app.blade.php`) |
| `<x-modal />` | `id`, `title`, `show` | Generic container modal component | Reusable modal dialogs |
| `<x-icon />` | `name` | FontAwesome icon wrapper component | Navigation and UI items |
