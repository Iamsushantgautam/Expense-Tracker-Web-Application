<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $userCount = User::count();
        $expenseCount = Expense::count();
        $totalExpenseVolume = Expense::sum('amount');
        $openSupportCount = SupportMessage::where('status', 'open')->count();

        $recentUsers = User::latest()->limit(5)->get();
        $recentSupportMessages = SupportMessage::with('user')->latest()->limit(5)->get();

        return view('admin.index', compact(
            'userCount',
            'expenseCount',
            'totalExpenseVolume',
            'openSupportCount',
            'recentUsers',
            'recentSupportMessages'
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  USER MANAGEMENT CRUD
    // ─────────────────────────────────────────────────────────────

    public function users(Request $request): View
    {
        $query = User::withCount('expenses')
            ->withSum('expenses', 'amount');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'monthly_budget' => 'nullable|numeric|min:0',
            'is_admin' => 'nullable|boolean',
        ]);

        $email = strtolower(trim($request->email));
        $isAdmin = ($email === 'sushantgautamlk6393@gmail.com') ? true : (bool) $request->is_admin;

        User::create([
            'name' => $request->name,
            'username' => strtolower(trim($request->username)),
            'email' => $email,
            'password' => Hash::make($request->password),
            'monthly_budget' => $request->monthly_budget ?? 10000.00,
            'budget_warn_limit' => 90,
            'theme_color' => 'blue',
            'dark_mode' => false,
            'is_admin' => $isAdmin,
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'New user account created successfully.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'monthly_budget' => 'nullable|numeric|min:0',
            'budget_warn_limit' => 'nullable|integer|min:1|max:100',
            'is_admin' => 'nullable|boolean',
        ]);

        $email = strtolower(trim($request->email));
        $isAdmin = ($email === 'sushantgautamlk6393@gmail.com') ? true : (bool) $request->is_admin;

        $data = [
            'name' => $request->name,
            'username' => strtolower(trim($request->username)),
            'email' => $email,
            'monthly_budget' => $request->monthly_budget ?? $user->monthly_budget,
            'budget_warn_limit' => $request->budget_warn_limit ?? $user->budget_warn_limit,
            'is_admin' => $isAdmin,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', 'User details updated successfully.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->id === Auth::id() || strtolower(trim($user->email)) === 'sushantgautamlk6393@gmail.com') {
            return back()->with('error', 'Action prohibited: The primary administrator account cannot be deleted.');
        }

        // Delete user's related data
        $user->expenses()->delete();
        $user->incomes()->delete();
        $user->categories()->delete();
        $user->supportMessages()->delete();
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User account and associated records deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────
    //  SUPPORT TICKET CRUD
    // ─────────────────────────────────────────────────────────────

    public function supportMessages(Request $request): View
    {
        $query = SupportMessage::with('user');

        if ($request->filled('status') && in_array($request->status, ['open', 'resolved'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $activeStatus = $request->query('status', 'all');

        return view('admin.support', compact('messages', 'activeStatus'));
    }

    public function storeSupportMessage(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'status' => 'required|in:open,resolved',
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        SupportMessage::create([
            'user_id' => $user?->id,
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.support')
            ->with('success', 'Support ticket logged successfully.');
    }

    public function updateSupportStatus(Request $request, SupportMessage $supportMessage): RedirectResponse
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'status' => 'required|in:open,resolved',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('name')) {
            $data['name'] = $request->name;
        }
        if ($request->filled('email')) {
            $data['email'] = strtolower(trim($request->email));
        }
        if ($request->filled('subject')) {
            $data['subject'] = $request->subject;
        }
        if ($request->filled('message')) {
            $data['message'] = $request->message;
        }

        $supportMessage->update($data);

        return back()->with('success', 'Support ticket #' . $supportMessage->ticket_number . ' updated successfully.');
    }

    public function destroySupportMessage(SupportMessage $supportMessage): RedirectResponse
    {
        $ticketNum = $supportMessage->ticket_number;
        $supportMessage->delete();

        return redirect()->route('admin.support')
            ->with('success', 'Support ticket #' . $ticketNum . ' deleted successfully.');
    }
}
