<x-app-layout title="Income Tracker" active="incomes">
    <div 
        x-data="{
            filterModalOpen: false,
            addModalOpen: false,
            editModalOpen: false,
            editIncome: {
                id: null,
                title: '',
                amount: '',
                source: '',
                payment_method: '',
                date: '',
                notes: '',
                updateUrl: '',
                deleteUrl: ''
            },
            openEditModal(data) {
                this.editIncome = {
                    id: data.id,
                    title: data.title,
                    amount: data.amount,
                    source: data.source,
                    payment_method: data.payment_method,
                    date: data.date,
                    notes: data.notes || '',
                    updateUrl: data.updateUrl,
                    deleteUrl: data.deleteUrl
                };
                this.editModalOpen = true;
            }
        }"
        @open-edit-income.window="openEditModal($event.detail)"
        @open-add-income.window="addModalOpen = true"
        class="space-y-6"
    >

        <!-- Page Header & Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    Income Management
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Track your earnings, salary, freelance payouts, and investments
                </p>
            </div>
            <div>
                <button
                    type="button"
                    @click="addModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 bg-primary text-white bg-primary-hover border border-transparent shadow-xs shadow-primary/20"
                >
                    <i class="fas fa-plus text-current"></i>
                    <span>Add New Income</span>
                </button>
            </div>
        </div>

        <!-- Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Total Income Card -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 btn-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total All-Time Income</p>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                        + ₹ {{ number_format($totalIncome, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>

            <!-- Current Month Income Card -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 btn-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">This Month's Income</p>
                    <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                        + ₹ {{ number_format($currentMonthIncome, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 flex items-center justify-center shrink-0 border border-sky-500/20">
                    <i class="fas fa-calendar-alt text-xl"></i>
                </div>
            </div>

            <!-- Total Income Records -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 btn-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Income Entries</p>
                    <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">
                        {{ number_format($totalEntries) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20">
                    <i class="fas fa-list-check text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Clean Control Bar (Search + Filter Modal Trigger Button) -->
        <div class="flat-card p-3 flex items-center justify-between gap-3 shadow-xs">
            <!-- Search Bar -->
            <x-search-bar
                :action="route('incomes.index')"
                placeholder="Search income title, source or notes..."
                :hidden-fields="[
                    'source' => request('source'),
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to')
                ]"
            />

            <!-- Filter Modal Trigger Button -->
            @php
                $activeFilterCount = (request('source') ? 1 : 0) + (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) + (request('search') ? 1 : 0);
            @endphp
            <button
                type="button"
                @click="filterModalOpen = true"
                class="px-3.5 py-1.5 text-xs font-semibold btn-rounded border flex items-center gap-2 transition-all duration-150 shrink-0 cursor-pointer
                {{ $activeFilterCount > 0 ? 'bg-primary/10 border-primary/30 text-primary font-bold' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}"
            >
                <i class="fas fa-sliders text-xs"></i>
                <span>Filter</span>
                @if($activeFilterCount > 0)
                    <span class="w-4 h-4 rounded-full bg-primary text-white text-[10px] flex items-center justify-center font-bold">
                        {{ $activeFilterCount }}
                    </span>
                @endif
            </button>
        </div>

        <!-- Active Filters Summary Pills -->
        @if(request()->anyFilled(['search', 'source', 'date_from', 'date_to']))
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <span class="text-slate-500 font-medium">Active Filters:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('incomes.index', request()->except('search')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('source'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        Source: {{ request('source') }}
                        <a href="{{ route('incomes.index', request()->except('source')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        From: {{ request('date_from') }}
                        <a href="{{ route('incomes.index', request()->except('date_from')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        To: {{ request('date_to') }}
                        <a href="{{ route('incomes.index', request()->except('date_to')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                <a href="{{ route('incomes.index') }}" class="text-xs font-semibold text-primary hover:underline ml-1">
                    Clear All Filters
                </a>
            </div>
        @endif

        <!-- INCOMES CARD COMPONENT LIST -->
        @if($incomes->count() > 0)
            <div class="space-y-4">
                <div class="flat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80 shadow-xs">
                    @foreach($incomes as $inc)
                        <x-transaction-card :transaction="$inc" />
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div>
                    {{ $incomes->links() }}
                </div>
            </div>
        @else
            <div class="flat-card p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded">
                <x-empty-state
                    title="No Income Records Found"
                    description="Start tracking your cash flow by recording your salary or income payouts."
                    icon="fas fa-wallet"
                    actionLabel="Add First Income Entry"
                    actionEvent="open-add-income"
                />
            </div>
        @endif

        <!-- REUSABLE FILTER MODAL COMPONENT -->
        <x-filter-modal
            :action="route('incomes.index')"
            :sources="$sources"
            :resetUrl="route('incomes.index')"
        />

        <!-- ADD & EDIT INCOME MODALS -->
        @include('incomes.partials.modals')

    </div>
</x-app-layout>
