<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $now = Carbon::now();

        // 1. Current Month Totals
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $thisMonthExpense = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        // 2. Previous Month Totals (for % change)
        $startOfPrevMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfPrevMonth = $now->copy()->subMonth()->endOfMonth();

        $prevMonthExpense = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$startOfPrevMonth, $endOfPrevMonth])
            ->sum('amount');

        $percentChange = 0;
        if ($prevMonthExpense > 0) {
            $percentChange = (($thisMonthExpense - $prevMonthExpense) / $prevMonthExpense) * 100;
        }

        // 3. Lifetime / Period Total Expenses
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');

        // 4. Average Daily Spend (Current Month)
        $daysPassed = max(1, $now->day);
        $avgDailySpend = $thisMonthExpense / $daysPassed;

        // 5. Remaining Budget & Budget Warning Status
        $budget = (float) $user->monthly_budget;
        $remainingBudget = max(0, $budget - $thisMonthExpense);
        $budgetUsedPercent = $budget > 0 ? min(100, ($thisMonthExpense / $budget) * 100) : 0;
        $isOverWarningLimit = $budget > 0 && $budgetUsedPercent >= $user->budget_warn_limit;

        // 6. Recent Transactions (Top 6)
        $recentTransactions = Expense::with('categoryModel')
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 7. Spending by Category (Current Month)
        $categoryBreakdown = Expense::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // 8. Income Stats
        $thisMonthIncome = \App\Models\Income::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        $totalIncome = \App\Models\Income::where('user_id', $user->id)->sum('amount');
        $netSavings = $totalIncome - $totalExpenses;

        // 9. Categories for Expense & Income Modals
        $expenseCategories = \App\Models\Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->where('status', 'active')->get();

        $incomeCategories = \App\Models\Category::where('type', 'income')
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })->orderBy('name')->pluck('name');

        if ($incomeCategories->isEmpty()) {
            $incomeCategories = collect(['Salary', 'Freelance', 'Investment', 'Business', 'Rental Income', 'Bonus & Rewards', 'Other Income']);
        }

        return view('dashboard.index', compact(
            'user',
            'thisMonthExpense',
            'prevMonthExpense',
            'percentChange',
            'totalExpenses',
            'avgDailySpend',
            'budget',
            'remainingBudget',
            'budgetUsedPercent',
            'isOverWarningLimit',
            'recentTransactions',
            'categoryBreakdown',
            'thisMonthIncome',
            'totalIncome',
            'netSavings',
            'expenseCategories',
            'incomeCategories'
        ));
    }

    /**
     * API endpoint for Chart.js overview data (7d, 30d, 6m, 1y)
     */
    public function chartData(Request $request): JsonResponse
    {
        $user = Auth::user();
        $range = $request->get('range', '30d');
        $now = Carbon::now();

        $labels = [];
        $data = [];

        if ($range === '7d' || $range === '30d') {
            $daysCount = ($range === '7d') ? 7 : 30;
            $startDate = $now->copy()->subDays($daysCount - 1)->startOfDay();

            $expenses = Expense::where('user_id', $user->id)
                ->whereDate('date', '>=', $startDate->toDateString())
                ->selectRaw('DATE(date) as formatted_date, SUM(amount) as total')
                ->groupBy('formatted_date')
                ->pluck('total', 'formatted_date');

            for ($i = $daysCount - 1; $i >= 0; $i--) {
                $day = $now->copy()->subDays($i);
                $dateKey = $day->toDateString();
                $labels[] = $day->format('M d');
                $data[] = (float) ($expenses[$dateKey] ?? 0);
            }
        } else {
            $monthsCount = ($range === '6m') ? 6 : 12;
            $startDate = $now->copy()->subMonths($monthsCount - 1)->startOfMonth();

            $expenses = Expense::where('user_id', $user->id)
                ->whereDate('date', '>=', $startDate->toDateString())
                ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month_key, SUM(amount) as total')
                ->groupBy('month_key')
                ->pluck('total', 'month_key');

            for ($i = $monthsCount - 1; $i >= 0; $i--) {
                $month = $now->copy()->subMonths($i);
                $monthKey = $month->format('Y-m');
                $labels[] = $month->format('M Y');
                $data[] = (float) ($expenses[$monthKey] ?? 0);
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}
