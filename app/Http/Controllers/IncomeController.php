<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomeStoreRequest;
use App\Http\Requests\IncomeUpdateRequest;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Income::where('user_id', $user->id);

        // Filter: Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter: Source
        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        // Sorting
        $sort = $request->input('sort', 'date');
        $direction = $request->input('direction', 'desc');

        if (in_array($sort, ['date', 'amount', 'title', 'created_at'])) {
            $query->orderBy($sort, strtolower($direction) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $incomes = $query->paginate(15)->withQueryString();

        // Calculate Summary Stats
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $currentMonthIncome = Income::where('user_id', $user->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->sum('amount');
        $totalEntries = Income::where('user_id', $user->id)->count();

        // Distinct Sources for filter
        $sources = Income::where('user_id', $user->id)
            ->distinct()
            ->pluck('source');

        if ($sources->isEmpty()) {
            $sources = collect(['Salary', 'Freelance', 'Investment', 'Business', 'Bonus', 'Other']);
        }

        // Income categories / sources for Add modal
        $categories = \App\Models\Category::where('type', 'income')
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })->orderBy('name')->pluck('name');

        if ($categories->isEmpty()) {
            $categories = collect(['Salary', 'Freelance', 'Investment', 'Business', 'Rental Income', 'Bonus & Rewards', 'Other Income']);
        }

        return view('incomes.index', compact('incomes', 'totalIncome', 'currentMonthIncome', 'totalEntries', 'sources', 'categories'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('incomes.index');
    }

    public function store(IncomeStoreRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        Income::create([
            'user_id' => $user->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'source' => $validated['source'],
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Income entry added successfully!');
    }

    public function edit(Income $income): RedirectResponse
    {
        return redirect()->route('incomes.index');
    }

    public function update(IncomeUpdateRequest $request, Income $income): RedirectResponse
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        $income->update([
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'source' => $validated['source'],
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', 'Income entry updated successfully!');
    }

    public function destroy(Income $income): RedirectResponse
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $income->delete();

        return redirect()->back()
            ->with('success', 'Income entry deleted successfully.');
    }
}
