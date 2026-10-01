<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Categories
        $defaultCategories = [
            ['name' => 'Food', 'icon' => 'utensils', 'color' => '#ef4444'],
            ['name' => 'Grocery', 'icon' => 'shopping-cart', 'color' => '#10b981'],
            ['name' => 'Travel', 'icon' => 'car', 'color' => '#3b82f6'],
            ['name' => 'Shopping', 'icon' => 'shopping-bag', 'color' => '#ec4899'],
            ['name' => 'Bills', 'icon' => 'file-text', 'color' => '#f59e0b'],
            ['name' => 'Entertainment', 'icon' => 'film', 'color' => '#8b5cf6'],
            ['name' => 'Health', 'icon' => 'activity', 'color' => '#06b6d4'],
            ['name' => 'Education', 'icon' => 'book-open', 'color' => '#6366f1'],
            ['name' => 'Fee', 'icon' => 'credit-card', 'color' => '#64748b'],
            ['name' => 'Exchange', 'icon' => 'repeat', 'color' => '#059669'],
            ['name' => 'Profit', 'icon' => 'trending-up', 'color' => '#10b981'],
            ['name' => 'Other', 'icon' => 'grid', 'color' => '#94a3b8'],
        ];

        $categoryMap = [];
        foreach ($defaultCategories as $catData) {
            $cat = Category::updateOrCreate(
                ['name' => $catData['name'], 'user_id' => null],
                [
                    'slug' => Str::slug($catData['name']),
                    'icon' => $catData['icon'],
                    'color' => $catData['color'],
                    'is_default' => true,
                    'status' => 'active',
                ]
            );
            $categoryMap[strtolower($catData['name'])] = $cat->id;
        }

        // 2. Create Default Users (Migrated & Admin)
        $admin = User::updateOrCreate(
            ['email' => 'admin@expensetracker.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'monthly_budget' => 25000.00,
                'budget_warn_limit' => 90,
                'is_admin' => false,
                'theme_color' => 'blue',
                'dark_mode' => false,
            ]
        );

        $sushant = User::updateOrCreate(
            ['email' => 'sushantgautamlk6393@gmail.com'],
            [
                'name' => 'Sushant Gautam',
                'password' => Hash::make('password123'),
                'monthly_budget' => 10000.00,
                'budget_warn_limit' => 90,
                'is_admin' => true,
                'theme_color' => 'blue',
                'dark_mode' => false,
            ]
        );

        $demoUser = User::updateOrCreate(
            ['email' => 'user@expensetracker.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password123'),
                'monthly_budget' => 15000.00,
                'budget_warn_limit' => 85,
                'is_admin' => false,
                'theme_color' => 'indigo',
                'dark_mode' => false,
            ]
        );

        // 3. Migrate / Seed Real Sample Expenses for Users
        $sampleExpenses = [
            [
                'user_id' => $sushant->id,
                'title' => 'Grocery Store Purchase',
                'amount' => 1000.00,
                'category' => 'Grocery',
                'date' => '2026-09-24',
                'payment_method' => 'UPI',
                'notes' => 'Weekly grocery and vegetables',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Daily Milk & Dairy',
                'amount' => 450.00,
                'category' => 'Grocery',
                'date' => '2026-09-25',
                'payment_method' => 'UPI',
                'notes' => 'Milk supply for month',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Metro & Cab Fare',
                'amount' => 350.00,
                'category' => 'Travel',
                'date' => '2026-09-26',
                'payment_method' => 'UPI',
                'notes' => 'Commute to office',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Lunch with Colleagues',
                'amount' => 680.00,
                'category' => 'Food',
                'date' => '2026-09-26',
                'payment_method' => 'Card',
                'notes' => 'Restaurant bill payment',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Internet & Wi-Fi Bill',
                'amount' => 899.00,
                'category' => 'Bills',
                'date' => '2026-09-20',
                'payment_method' => 'Net Banking',
                'notes' => 'Monthly broadband bill',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Electricity Bill',
                'amount' => 1420.00,
                'category' => 'Bills',
                'date' => '2026-09-15',
                'payment_method' => 'UPI',
                'notes' => 'Monthly power supply',
            ],
            [
                'user_id' => $sushant->id,
                'title' => 'Books & Stationeries',
                'amount' => 650.00,
                'category' => 'Education',
                'date' => '2026-09-10',
                'payment_method' => 'UPI',
                'notes' => 'Course reference materials',
            ],
            [
                'user_id' => $demoUser->id,
                'title' => 'Supermarket Grocery',
                'amount' => 2450.00,
                'category' => 'Grocery',
                'date' => '2026-09-22',
                'payment_method' => 'Card',
                'notes' => 'Household supplies',
            ],
            [
                'user_id' => $demoUser->id,
                'title' => 'Fuel Refill',
                'amount' => 1500.00,
                'category' => 'Travel',
                'date' => '2026-09-25',
                'payment_method' => 'Cash',
                'notes' => 'Petrol tank refill',
            ],
            [
                'user_id' => $demoUser->id,
                'title' => 'Movie Tickets',
                'amount' => 700.00,
                'category' => 'Entertainment',
                'date' => '2026-09-21',
                'payment_method' => 'UPI',
                'notes' => 'Weekend cinema',
            ],
        ];

        foreach ($sampleExpenses as $exp) {
            $catKey = strtolower($exp['category']);
            $catId = $categoryMap[$catKey] ?? null;

            Expense::create([
                'user_id' => $exp['user_id'],
                'category_id' => $catId,
                'title' => $exp['title'],
                'amount' => $exp['amount'],
                'category' => $exp['category'],
                'date' => $exp['date'],
                'payment_method' => $exp['payment_method'],
                'notes' => $exp['notes'],
            ]);
        }

        // 4. Seed Support Messages
        SupportMessage::create([
            'user_id' => $sushant->id,
            'name' => 'Sushant Gautam',
            'email' => 'sushantgautamlk6393@gmail.com',
            'subject' => 'Expense Category Customization',
            'message' => 'Hello team, how can I add custom categories to my expense tracker?',
            'status' => 'open',
        ]);
    }
}
