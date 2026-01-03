# Expense Tracker

This is a modern full-stack Expense Tracker (PHP + MySQL + HTML/CSS/JS).

## Features
- Dashboard with charts and budget summary
- Add, edit, delete, and filter expenses
- CSV export for expenses
- Profile page with image upload and password change
- Dark/Light mode with theme toggle (sidebar)
- Responsive, mobile-friendly UI
- Font Awesome icons throughout
- Help & Support page (messages stored in database)

## Setup
1. Import `db.sql` into your MySQL database (creates all tables, including `support_messages`).
2. Update `config.php` with your database credentials.
3. Ensure `uploads/profile_pics` is writable by the web server.
4. Run with Apache or: `php -S localhost:8000` from the project folder.

## Pages
- `dashboard.php` — Overview and charts
- `expenses.php` — List, filter, export expenses
- `add_expense.php` / `edit_expense.php` — Add or edit an expense
- `profile.php` — Update profile, upload photo, change password
- `help.php` — Contact support (messages saved in DB)

## Support Messages
All messages sent from the Help & Support page are stored in the `support_messages` table for admin review.

---
For any issues, use the Help & Support page in the app!
