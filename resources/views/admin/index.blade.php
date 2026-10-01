<x-app-layout title="Admin Panel" active="admin">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-primary"></i> Admin Overview
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    System-wide platform management and metrics
                </p>
            </div>
            <div class="flex items-center gap-2">
                <x-button :href="route('admin.support')" variant="outline" size="sm" icon="fas fa-headset">
                    Manage Support
                </x-button>
                <x-button :href="route('admin.users')" variant="primary" size="sm" icon="fas fa-users">
                    Manage Users
                </x-button>
            </div>
        </div>

        <!-- Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-stat-card
                title="Registered Users"
                value="{{ number_format($userCount) }}"
                icon="fas fa-users"
            />
            <x-stat-card
                title="Total Expenses Logged"
                value="{{ number_format($expenseCount) }}"
                icon="fas fa-receipt"
            />
            <x-stat-card
                title="Total Expense Volume"
                value="₹{{ number_format($totalExpenseVolume, 2) }}"
                icon="fas fa-chart-bar"
            />
            <x-stat-card
                title="Open Support Tickets"
                value="{{ number_format($openSupportCount) }}"
                icon="fas fa-life-ring"
            />
        </div>

        <!-- Recent System Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Recent Users -->
            <div class="flat-card p-5 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Recently Registered Users</h3>
                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($recentUsers as $u)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $u->name }}</p>
                                <p class="text-slate-400">{{ $u->email }}</p>
                            </div>
                            <span class="text-slate-400 font-mono">
                                {{ \Carbon\Carbon::parse($u->created_at)->format('M d, Y') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Support Requests -->
            <div class="flat-card p-5 space-y-4">
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Pending Support Requests</h3>
                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach($recentSupportMessages as $msg)
                        <div class="py-2.5 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $msg->subject }}</p>
                                <p class="text-slate-400">By {{ $msg->name }} ({{ $msg->email }})</p>
                            </div>
                            <span class="px-2 py-0.5 btn-rounded text-[10px] font-semibold {{ $msg->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ strtoupper($msg->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
