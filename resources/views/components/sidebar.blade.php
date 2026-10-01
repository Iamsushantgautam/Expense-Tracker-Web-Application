@props([
    'active' => 'dashboard'
])

@php
    $user = auth()->user();
    $avatar = $user->profile_pic ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=ffffff&background=2563eb';

    $navItems = [
        ['key' => 'dashboard', 'route' => route('dashboard'), 'label' => 'Dashboard', 'icon' => 'fas fa-chart-line'],
        ['key' => 'transactions', 'route' => route('transactions.index'), 'label' => 'All Transactions', 'icon' => 'fas fa-list-check'],
        ['key' => 'incomes', 'route' => route('incomes.index'), 'label' => 'Income Tracker', 'icon' => 'fas fa-wallet'],
        ['key' => 'expenses', 'route' => route('expenses.index'), 'label' => 'Expenses', 'icon' => 'fas fa-receipt'],
        ['key' => 'reports', 'route' => route('reports.index'), 'label' => 'Reports', 'icon' => 'fas fa-pie-chart'],
        ['key' => 'categories', 'route' => route('categories.index'), 'label' => 'Categories', 'icon' => 'fas fa-tags'],
        ['key' => 'profile', 'route' => route('profile.edit'), 'label' => 'Profile & Theme', 'icon' => 'fas fa-user-cog'],
        ['key' => 'support', 'route' => route('support.index'), 'label' => 'Help & Support', 'icon' => 'fas fa-headset'],
    ];

    if ($user && $user->is_admin) {
        $navItems[] = ['key' => 'admin', 'route' => route('admin.index'), 'label' => 'Admin Panel', 'icon' => 'fas fa-shield-alt'];
    }
@endphp

<!-- Desktop Sidebar -->
<aside class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 z-30 transition-colors duration-150">
    <!-- Logo & Brand Header -->
    <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-primary text-white flex items-center justify-center font-bold text-lg">
                ₹
            </div>
            <div class="font-bold text-base tracking-tight text-slate-900 dark:text-slate-100">
                Wit Expense<span class="text-primary">Tracker</span>
            </div>
        </a>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 flex flex-col justify-between overflow-y-auto px-4 py-4">
        <nav class="space-y-1">
            <div class="px-2 mb-2 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                Main Menu
            </div>

            @foreach($navItems as $item)
                @php
                    $isActive = $active === $item['key'] || request()->routeIs($item['key'] . '*');
                @endphp
                <a
                    href="{{ $item['route'] }}"
                    class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium btn-rounded transition-colors duration-150
                    {{ $isActive ? 'bg-primary-light text-primary font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-slate-200' }}"
                >
                    <i class="{{ $item['icon'] }} text-base shrink-0 w-5 text-center"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <!-- Quick Theme Switch & User Footer -->
        <div class="space-y-4 pt-4 border-t border-slate-200 dark:border-slate-800">
            <!-- Quick Dark Mode Toggle -->
            <button
                type="button"
                x-data
                @click="window.ExpenseTracker.toggleDarkMode()"
                class="group w-full flex items-center justify-between px-3 py-2 text-xs font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 btn-rounded transition-all duration-200 active:scale-95 cursor-pointer"
            >
                <span class="flex items-center gap-2">
                    <!-- Sun Icon (Light Mode Only) -->
                    <span class="dark:hidden inline-flex items-center justify-center transition-transform duration-500 transform group-hover:rotate-90 group-hover:scale-110">
                        <i class="fas fa-sun text-amber-500 text-sm"></i>
                    </span>
                    <!-- Moon Icon (Dark Mode Only) -->
                    <span class="hidden dark:inline-flex items-center justify-center transition-transform duration-500 transform group-hover:-rotate-45 group-hover:scale-110">
                        <i class="fas fa-moon text-sky-400 text-sm"></i>
                    </span>
                    <span>Toggle Theme</span>
                </span>
                <span class="text-[11px] uppercase font-bold tracking-wider">
                    <span class="dark:hidden text-amber-600">Light</span>
                    <span class="hidden dark:inline text-sky-400">Dark</span>
                </span>
            </button>

            <!-- User Account Summary -->
            <div class="flex items-center justify-between pt-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 group overflow-hidden">
                    <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="truncate">
                        <p class="text-xs font-semibold text-slate-900 dark:text-slate-100 truncate group-hover:text-primary transition-colors">
                            {{ $user->name }}
                        </p>
                        <p class="text-[11px] text-slate-400 truncate">
                            {{ $user->email }}
                        </p>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors" title="Logout">
                        <i class="fas fa-sign-out-alt text-base"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
