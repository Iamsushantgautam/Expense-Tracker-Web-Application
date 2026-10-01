<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $typeFilter = $request->input('type', 'all'); // 'all', 'income', 'expense'

        // 1. Build subqueries for Income and Expense
        $incomeQuery = DB::table('incomes')
            ->select(
                'id',
                DB::raw("'income' as transaction_type"),
                'title',
                'amount',
                'source as category_or_source',
                'date',
                'payment_method',
                'notes',
                'created_at',
                DB::raw("NULL as receipt_url")
            )
            ->where('user_id', $user->id);

        $expenseQuery = DB::table('expenses')
            ->select(
                'id',
                DB::raw("'expense' as transaction_type"),
                'title',
                'amount',
                'category as category_or_source',
                'date',
                'payment_method',
                'notes',
                'created_at',
                'receipt_url'
            )
            ->where('user_id', $user->id);

        // Filter: Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $incomeQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
            $expenseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $dateFrom = $request->input('date_from');
            $incomeQuery->whereDate('date', '>=', $dateFrom);
            $expenseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($request->filled('date_to')) {
            $dateTo = $request->input('date_to');
            $incomeQuery->whereDate('date', '<=', $dateTo);
            $expenseQuery->whereDate('date', '<=', $dateTo);
        }

        // Apply Type Filter
        if ($typeFilter === 'income') {
            $unifiedQuery = $incomeQuery;
        } elseif ($typeFilter === 'expense') {
            $unifiedQuery = $expenseQuery;
        } else {
            $unifiedQuery = $incomeQuery->unionAll($expenseQuery);
        }

        // Sorting & Pagination
        $sort = $request->input('sort', 'date');
        $direction = strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $transactions = DB::query()
            ->fromSub($unifiedQuery, 'all_txns')
            ->orderBy($sort, $direction)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        // Calculate Totals
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpenses = Expense::where('user_id', $user->id)->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;
        $totalCount = Income::where('user_id', $user->id)->count() + Expense::where('user_id', $user->id)->count();

        // Expense categories for Add Expense modal
        $expenseCategories = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->where('status', 'active')->get();

        // Income categories for Add Income modal
        $incomeCategories = Category::where('type', 'income')
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })->orderBy('name')->pluck('name');

        if ($incomeCategories->isEmpty()) {
            $incomeCategories = collect(['Salary', 'Freelance', 'Investment', 'Business', 'Rental Income', 'Bonus & Rewards', 'Other Income']);
        }

        return view('transactions.index', compact(
            'transactions',
            'typeFilter',
            'totalIncome',
            'totalExpenses',
            'netBalance',
            'totalCount',
            'expenseCategories',
            'incomeCategories'
        ));
    }
}
