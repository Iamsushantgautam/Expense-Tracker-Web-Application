<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Services\CloudinaryService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Expense::with('categoryModel')
            ->where('user_id', $user->id);

        // Filter: Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter: Category
        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->where(function ($q) use ($category) {
                $q->where('category', $category)
                  ->orWhereHas('categoryModel', function ($catQuery) use ($category) {
                      $catQuery->where('slug', $category)->orWhere('name', $category);
                  });
            });
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        // Filter: Amount Range
        if ($request->filled('amount_min')) {
            $query->where('amount', '>=', $request->input('amount_min'));
        }
        if ($request->filled('amount_max')) {
            $query->where('amount', '<=', $request->input('amount_max'));
        }

        // Sorting
        $sort = $request->input('sort', 'date');
        $direction = $request->input('direction', 'desc');

        if (in_array($sort, ['date', 'amount', 'title', 'created_at'])) {
            $query->orderBy($sort, strtolower($direction) === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('date', 'desc');
        }

        $expenses = $query->paginate(15)->withQueryString();

        // Get Available Categories for Filter Dropdown
        $categories = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->where('status', 'active')->get();

        return view('expenses.index', compact('expenses', 'categories'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('expenses.index');
    }

    public function store(ExpenseStoreRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $receiptUrl = null;
        $receiptPublicId = null;

        // Upload receipt to Cloudinary inside folder WitExpenseTracker/{username}/expenses
        if ($request->hasFile('receipt')) {
            $uploadResult = $this->cloudinaryService->upload(
                $request->file('receipt'),
                $user->username ?? $user->name,
                'expenses'
            );

            if ($uploadResult) {
                $receiptUrl = $uploadResult['url'];
                $receiptPublicId = $uploadResult['public_id'];
            }
        }

        // Determine category string and category_id
        $categoryName = $validated['category'] ?? 'Other';
        if (!empty($validated['category_id'])) {
            $catObj = Category::find($validated['category_id']);
            if ($catObj) {
                $categoryName = $catObj->name;
            }
        }

        Expense::create([
            'user_id' => $user->id,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $categoryName,
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'receipt_url' => $receiptUrl,
            'receipt_public_id' => $receiptPublicId,
        ]);

        return redirect()->back()
            ->with('success', 'Expense created successfully!');
    }

    public function show(Expense $expense): View
    {
        Gate::authorize('view', $expense);

        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): RedirectResponse
    {
        return redirect()->route('expenses.index');
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $user = Auth::user();
        $validated = $request->validated();

        $receiptUrl = $expense->receipt_url;
        $receiptPublicId = $expense->receipt_public_id;

        if ($request->hasFile('receipt')) {
            $uploadResult = $this->cloudinaryService->upload(
                $request->file('receipt'),
                $user->username ?? $user->name,
                'expenses'
            );

            if ($uploadResult) {
                $receiptUrl = $uploadResult['url'];
                $receiptPublicId = $uploadResult['public_id'];
            }
        }

        $categoryName = $validated['category'] ?? $expense->category;
        if (!empty($validated['category_id'])) {
            $catObj = Category::find($validated['category_id']);
            if ($catObj) {
                $categoryName = $catObj->name;
            }
        }

        $expense->update([
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'category' => $categoryName,
            'date' => $validated['date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'receipt_url' => $receiptUrl,
            'receipt_public_id' => $receiptPublicId,
        ]);

        return redirect()->back()
            ->with('success', 'Expense updated successfully!');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $expense->delete();

        return redirect()->back()
            ->with('success', 'Expense deleted successfully.');
    }

    /**
     * CSV Export Functionality
     */
    public function export(Request $request): StreamedResponse
    {
        $user = Auth::user();
        $filename = 'expenses-' . Carbon::now()->format('Y-m-d') . '.csv';

        $query = Expense::where('user_id', $user->id);

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->input('date_to'));
        }

        $expenses = $query->orderBy('date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Date', 'Expense Title', 'Category', 'Payment Method', 'Amount (INR)', 'Notes']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->id,
                    Carbon::parse($expense->date)->format('Y-m-d'),
                    $expense->title,
                    $expense->category,
                    $expense->payment_method,
                    $expense->amount,
                    $expense->notes,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
