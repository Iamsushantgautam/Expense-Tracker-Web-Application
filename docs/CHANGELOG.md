# Project Changelog

All notable changes to the WitExpenseTracker project will be documented in this file.

| Date | File Changed | What Changed | Why |
| --- | --- | --- | --- |
| 2026-09-30 | `expenses/partials/modals.blade.php` | Made open state variables dynamic (`$addModalVar`, `$editModalVar`) | Allowed multiple modal partials (Income + Expense) on unified views without variable collision |
| 2026-09-30 | `incomes/partials/modals.blade.php` | Made open state variables dynamic (`$addModalVar`, `$editModalVar`) | Allowed multiple modal partials (Income + Expense) on unified views without variable collision |
| 2026-09-30 | `transactions/index.blade.php` | Included modal partials with category variables and Alpine controls | Enabled full Add/Edit/Delete expense & income modal functionality directly from All Transactions view |
| 2026-09-30 | `dashboard/index.blade.php` | Integrated Alpine modal state & partial inclusions | Enabled instant Add/Edit expense & income modals directly from Dashboard page |
| 2026-09-30 | `DashboardController.php` | Added `$expenseCategories` and `$incomeCategories` to view compact | Populated modal dropdowns on Dashboard |
| 2026-09-30 | `ExpenseController.php` & `IncomeController.php` | Changed redirects to `redirect()->back()` | Maintained user context when submitting modals from multi-entity pages |
| 2026-09-30 | `/docs/*` & root rule files | Created comprehensive project documentation suite and auto-update rules | Standardized documentation architecture and LLM context |
