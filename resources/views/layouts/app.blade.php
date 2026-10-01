<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full light" data-theme="{{ auth()->user()->theme_color ?? 'blue' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Wit Expense Tracker') }}</title>

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#2563eb">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Inline Script for Early Theme Initialization to Prevent Theme Flash -->
    <script>
        (function() {
            const savedColor = localStorage.getItem('theme_color') || '{{ auth()->user()->theme_color ?? "blue" }}';
            const savedDark = localStorage.getItem('dark_mode');

            document.documentElement.setAttribute('data-theme', savedColor);

            if (savedDark === 'true') {
                document.documentElement.classList.add('dark');
            } else if (savedDark === 'false') {
                document.documentElement.classList.remove('dark');
            } else if ({{ auth()->user()->dark_mode ? 'true' : 'false' }}) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ mobileSidebarOpen: false }"
    @toggle-mobile-sidebar.window="mobileSidebarOpen = !mobileSidebarOpen"
    class="h-full bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased font-sans transition-colors duration-150"
>

    <!-- Desktop Sidebar -->
    <x-sidebar :active="$active ?? 'dashboard'" />

    <!-- Mobile Slide-Over Navigation -->
    <div
        x-show="mobileSidebarOpen"
        x-cloak
        class="relative z-50 md:hidden"
        role="dialog"
        aria-modal="true"
    >
        <div
            x-show="mobileSidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
            @click="mobileSidebarOpen = false"
        ></div>

        <div class="fixed inset-0 flex">
            <div
                x-show="mobileSidebarOpen"
                x-transition:enter="transition ease-in-out duration-200 transform"
                x-transition:enter-start="-translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-150 transform"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="relative max-w-xs w-full bg-white dark:bg-slate-900 flex-1 flex flex-col pt-5 pb-4"
            >
                <div class="px-6 flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                    <div class="font-bold text-base text-slate-900 dark:text-slate-100">
                    Wit Expense<span class="text-primary">Tracker</span>
                    </div>
                    <button @click="mobileSidebarOpen = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div class="mt-4 flex-1 h-0 overflow-y-auto px-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-chart-line mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('transactions.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-list-check mr-2"></i> All Transactions
                    </a>
                    <a href="{{ route('incomes.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-wallet mr-2"></i> Income
                    </a>
                    <a href="{{ route('expenses.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-receipt mr-2"></i> Expenses
                    </a>
                    <a href="{{ route('reports.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-pie-chart mr-2"></i> Reports
                    </a>
                    <a href="{{ route('categories.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-tags mr-2"></i> Categories
                    </a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-user-cog mr-2"></i> Profile
                    </a>
                    <a href="{{ route('support.index') }}" class="block px-3 py-2 text-sm font-medium btn-rounded text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="fas fa-headset mr-2"></i> Help & Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Shell -->
    <div class="md:pl-64 flex flex-col flex-1 min-h-screen">
        <!-- Topbar Header -->
        <x-topbar :title="$title ?? 'Dashboard'" />

        <!-- Top-Right Toast Notifications Component -->
        <x-toast />

        <!-- Main Content Shell -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-6 max-w-7xl w-full mx-auto">
            {{ $slot }}
        </main>
    </div>

    <!-- Global Delete Confirmation Modal (Alpine.js) -->
    <div
        x-data="{ open: false, title: '', actionUrl: '' }"
        @open-delete-modal.window="open = true; title = $event.detail.title; actionUrl = $event.detail.action"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-xs"
    >
        <div @click.outside="open = false" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded max-w-md w-full p-6 space-y-4 shadow-none">
            <div class="flex items-center gap-3 text-red-600 dark:text-red-400">
                <i class="fas fa-exclamation-triangle text-xl"></i>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Delete Expense?</h3>
            </div>
            <p class="text-sm text-slate-600 dark:text-slate-400">
                Are you sure you want to delete <strong class="text-slate-900 dark:text-slate-200" x-text="title"></strong>? This action cannot be undone.
            </p>

            <form :action="actionUrl" method="POST" class="flex items-center justify-end gap-3 pt-2">
                @csrf
                @method('DELETE')
                <x-button type="button" variant="outline" @click="open = false">Cancel</x-button>
                <x-button type="submit" variant="danger" icon="fas fa-trash">Delete Expense</x-button>
            </form>
        </div>
    </div>

    <!-- Global Add Expense Modal (Triggered via Topbar Header CTA) -->
    <div
        x-data="{ globalAddExpenseOpen: false }"
        @open-global-add-expense.window="globalAddExpenseOpen = true"
    >
        @include('expenses.partials.modals', [
            'addModalVar' => 'globalAddExpenseOpen'
        ])
    </div>

</body>
</html>
