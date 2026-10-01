<x-app-layout title="All Transactions" active="transactions">
    <div
        x-data="{
            filterModalOpen: false,

            {{-- ── Add/Edit EXPENSE state ── --}}
            addExpenseModalOpen: false,
            editExpenseModalOpen: false,
            editExpense: {
                id: null,
                title: '',
                amount: '',
                category_id: '',
                payment_method: '',
                date: '',
                notes: '',
                receiptUrl: null,
                updateUrl: '',
                deleteUrl: ''
            },
            openEditExpenseModal(data) {
                this.editExpense = {
                    id: data.id,
                    title: data.title,
                    amount: data.amount,
                    category_id: data.category_id,
                    payment_method: data.payment_method,
                    date: data.date,
                    notes: data.notes || '',
                    receiptUrl: data.receiptUrl || null,
                    updateUrl: data.updateUrl,
                    deleteUrl: data.deleteUrl
                };
                this.editExpenseModalOpen = true;
            },

            {{-- ── Add/Edit INCOME state ── --}}
            addIncomeModalOpen: false,
            editIncomeModalOpen: false,
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
            openEditIncomeModal(data) {
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
                this.editIncomeModalOpen = true;
            }
        }"
        @open-edit-expense.window="openEditExpenseModal($event.detail)"
        @open-edit-income.window="openEditIncomeModal($event.detail)"
        class="space-y-6"
    >

        <!-- Header Title & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    All Transactions
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Unified timeline of all your earnings and expenses
                </p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3">
                <button
                    type="button"
                    @click="addIncomeModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 border border-emerald-600/40 text-emerald-700 dark:text-emerald-400 bg-white dark:bg-slate-900 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 shadow-xs"
                >
                    <i class="fas fa-plus text-current"></i>
                    <span>Add Income</span>
                </button>
                <button
                    type="button"
                    @click="addExpenseModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 bg-primary text-white bg-primary-hover border border-transparent shadow-xs shadow-primary/20"
                >
                    <i class="fas fa-minus text-current"></i>
                    <span>Add Expense</span>
                </button>
            </div>
        </div>

        <!-- Enhanced Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Card 1: Income -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Income Recorded</p>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 tracking-tight">
                        + ₹ {{ number_format($totalIncome, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center shrink-0 border border-emerald-500/20">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>

            <!-- Card 2: Expenses -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Total Expenses Recorded</p>
                    <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 tracking-tight">
                        - ₹ {{ number_format($totalExpenses, 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/20">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
            </div>

            <!-- Card 3: Net Cash Flow -->
            <div class="flat-card p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded flex items-center justify-between shadow-xs">
                <div class="space-y-1">
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Net Cash Flow Balance</p>
                    <p class="text-2xl font-extrabold tracking-tight {{ $netBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                        {{ $netBalance >= 0 ? '+' : '-' }} ₹ {{ number_format(abs($netBalance), 2) }}
                    </p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400 flex items-center justify-center shrink-0 border border-indigo-500/20">
                    <i class="fas fa-scale-balanced text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Clean Control Toolbar (Type Tabs + Search + Filter Modal Trigger) -->
        <div class="flat-card p-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shadow-xs">

            <!-- Type Pill Selector -->
            <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800/80 btn-rounded border border-slate-200 dark:border-slate-700/80 self-start sm:self-auto">
                <a
                    href="{{ route('transactions.index', array_merge(request()->except('type'), ['type' => 'all'])) }}"
                    class="px-3.5 py-1.5 text-xs font-semibold btn-rounded transition-all duration-150 {{ $typeFilter === 'all' ? 'bg-primary text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-list-ul mr-1.5"></i> All
                </a>
                <a
                    href="{{ route('transactions.index', array_merge(request()->except('type'), ['type' => 'income'])) }}"
                    class="px-3.5 py-1.5 text-xs font-semibold btn-rounded transition-all duration-150 {{ $typeFilter === 'income' ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-arrow-down-left text-emerald-400 mr-1.5"></i> Income
                </a>
                <a
                    href="{{ route('transactions.index', array_merge(request()->except('type'), ['type' => 'expense'])) }}"
                    class="px-3.5 py-1.5 text-xs font-semibold btn-rounded transition-all duration-150 {{ $typeFilter === 'expense' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200' }}"
                >
                    <i class="fas fa-arrow-up-right text-rose-400 mr-1.5"></i> Expenses
                </a>
            </div>

            <!-- Search Bar & Filter Modal Open Button -->
            <div class="flex items-center gap-2 flex-1 max-w-md ml-auto">
                <x-search-bar
                    :action="route('transactions.index')"
                    placeholder="Search title, category, or notes..."
                    :hidden-fields="[
                        'type' => $typeFilter,
                        'date_from' => request('date_from'),
                        'date_to' => request('date_to')
                    ]"
                />

                <!-- Filter Modal Trigger Button -->
                @php
                    $activeFilterCount = (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) + (request('search') ? 1 : 0);
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

        </div>

        <!-- Active Filters Summary Badge (If Any) -->
        @if(request()->anyFilled(['search', 'date_from', 'date_to']))
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <span class="text-slate-500 font-medium">Active Filters:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('transactions.index', request()->except('search')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        From: {{ request('date_from') }}
                        <a href="{{ route('transactions.index', request()->except('date_from')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        To: {{ request('date_to') }}
                        <a href="{{ route('transactions.index', request()->except('date_to')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                <a href="{{ route('transactions.index', ['type' => $typeFilter]) }}" class="text-xs font-semibold text-primary hover:underline ml-1">
                    Clear All Filters
                </a>
            </div>
        @endif

        <!-- TRANSACTIONS CARD COMPONENT LIST -->
        @if($transactions->count() > 0)
            <div class="space-y-4">
                <div class="flat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80 shadow-xs">
                    @foreach($transactions as $txn)
                        <x-transaction-card :transaction="$txn" />
                    @endforeach
                </div>

                <!-- Pagination Footer -->
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        @else
            <div class="flat-card p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded">
                <x-empty-state
                    title="No Transactions Found"
                    description="You haven't recorded any income or expense transactions matching this criteria."
                    icon="fas fa-receipt"
                />
            </div>
        @endif

        <!-- REUSABLE FILTER MODAL COMPONENT -->
        <x-filter-modal
            :action="route('transactions.index')"
            :typeFilter="$typeFilter"
            :resetUrl="route('transactions.index', ['type' => $typeFilter])"
        />

        <!-- ADD & EDIT EXPENSE MODALS -->
        @include('expenses.partials.modals', [
            'categories' => $expenseCategories,
            'addModalVar' => 'addExpenseModalOpen',
            'editModalVar' => 'editExpenseModalOpen'
        ])

        <!-- ADD & EDIT INCOME MODALS -->
        @include('incomes.partials.modals', [
            'categories' => $incomeCategories,
            'addModalVar' => 'addIncomeModalOpen',
            'editModalVar' => 'editIncomeModalOpen'
        ])

    </div>
</x-app-layout>
