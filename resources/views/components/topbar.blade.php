@props([
    'title' => 'Dashboard'
])

@php
    $user = auth()->user();
    $avatar = $user->profile_pic ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=ffffff&background=2563eb';
@endphp

<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-20 flex items-center justify-between px-4 sm:px-6 transition-colors duration-150">
    <div class="flex items-center gap-3">
        <!-- Mobile Sidebar Toggle Button -->
        <button
            type="button"
            x-data
            @click="$dispatch('toggle-mobile-sidebar')"
            class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 btn-rounded"
            title="Open Menu"
        >
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Page Title & Breadcrumb -->
        <div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-slate-100 tracking-tight">
                {{ $title }}
            </h1>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <!-- Add Expense Header Button (Quick CTA) -->
        <button
            type="button"
            x-data
            @click="$dispatch('open-add-expense-modal'); $dispatch('open-global-add-expense')"
            class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-3.5 py-1.5 text-xs sm:text-sm gap-2 bg-primary text-white hover:bg-primary-dark shadow-xs shadow-primary/20"
        >
            <i class="fas fa-plus text-xs"></i>
            <span class="hidden sm:inline">Add Expense</span>
        </button>

        <!-- Quick Theme Switch (Header) -->
        <button
            type="button"
            x-data
            @click="window.ExpenseTracker.toggleDarkMode()"
            class="group p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 btn-rounded transition-all duration-200 active:scale-95 cursor-pointer"
            title="Toggle Light/Dark Theme"
        >
            <span class="dark:hidden inline-flex items-center justify-center transition-transform duration-500 transform group-hover:rotate-90 group-hover:scale-110">
                <i class="fas fa-sun text-amber-500 text-sm"></i>
            </span>
            <span class="hidden dark:inline-flex items-center justify-center transition-transform duration-500 transform group-hover:-rotate-45 group-hover:scale-110">
                <i class="fas fa-moon text-sky-400 text-sm"></i>
            </span>
        </button>

        <!-- User Avatar & Profile Link -->
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 pl-2">
            <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700">
            <span class="hidden lg:inline text-xs font-semibold text-slate-700 dark:text-slate-300">
                {{ $user->name }}
            </span>
        </a>
    </div>
</header>
