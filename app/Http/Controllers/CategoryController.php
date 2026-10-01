<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        $baseQuery = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->orderBy('is_default', 'desc')->orderBy('name', 'asc');

        $expenseCategories = (clone $baseQuery)->where(function ($q) {
            $q->where('type', 'expense')->orWhereNull('type');
        })->get();

        $incomeCategories = (clone $baseQuery)->where('type', 'income')->get();

        $activeTab = $request->query('type', 'all');

        return view('categories.index', compact('expenseCategories', 'incomeCategories', 'activeTab'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:expense,income',
            'color' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();

        Category::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'type' => $request->type,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? ($request->type === 'income' ? 'wallet' : 'tag'),
            'color' => $request->color,
            'is_default' => false,
            'status' => 'active',
        ]);

        return redirect()->route('categories.index', ['type' => $request->type])
            ->with('success', ucfirst($request->type) . ' category created successfully!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $user = Auth::user();

        if (($category->is_default && !$user->is_admin) || ($category->user_id !== $user->id && !$user->is_admin)) {
            return back()->with('error', 'Default or protected categories cannot be edited.');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:expense,income',
            'color' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? ($request->type === 'income' ? 'wallet' : 'tag'),
            'color' => $request->color,
        ]);

        Expense::where('category_id', $category->id)->update([
            'category' => $request->name,
        ]);

        return redirect()->route('categories.index', ['type' => $request->type])
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $user = Auth::user();

        if ($category->is_default || ($category->user_id !== $user->id && !$user->is_admin)) {
            return back()->with('error', 'Default or protected categories cannot be deleted.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
