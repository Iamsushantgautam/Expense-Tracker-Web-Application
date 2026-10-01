<x-app-layout title="Expenses" active="expenses">
    <div 
        x-data="{
            filterModalOpen: false,
            addModalOpen: false,
            editModalOpen: false,
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
            openEditModal(data) {
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
                this.editModalOpen = true;
            }
        }"
        @open-edit-expense.window="openEditModal($event.detail)"
        @open-add-expense.window="addModalOpen = true"
        class="space-y-6"
    >

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                    Expense Management
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Manage, search, and export your recorded expenses
                </p>
            </div>
            <div class="flex items-center gap-3">
                <x-button :href="route('expenses.export', request()->query())" variant="outline" size="md" icon="fas fa-file-csv">
                    Export Filtered CSV
                </x-button>
                <button
                    type="button"
                    @click="addModalOpen = true"
                    class="relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none select-none cursor-pointer px-4 py-2 text-sm gap-2 bg-primary text-white bg-primary-hover border border-transparent shadow-xs"
                >
                    <i class="fas fa-plus text-current"></i>
                    <span>Add Expense</span>
                </button>
            </div>
        </div>

        <!-- Clean Control Bar (Search + Filter Modal Button) -->
        <div class="flat-card p-3 flex items-center justify-between gap-3 shadow-xs">
            <!-- Search Bar Component -->
            <x-search-bar
                :action="route('expenses.index')"
                placeholder="Search expense title or notes..."
                :hidden-fields="[
                    'category' => request('category'),
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to'),
                ]"
            />

            <!-- Filter Modal Button -->
            @php
                $activeFilterCount = (request('category') ? 1 : 0) + (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) + (request('search') ? 1 : 0);
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
        @if(request()->anyFilled(['search', 'category', 'date_from', 'date_to']))
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <span class="text-slate-500 font-medium">Active Filters:</span>
                @if(request('search'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        Search: "{{ request('search') }}"
                        <a href="{{ route('expenses.index', request()->except('search')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('category'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        Category: {{ request('category') }}
                        <a href="{{ route('expenses.index', request()->except('category')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_from'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        From: {{ request('date_from') }}
                        <a href="{{ route('expenses.index', request()->except('date_from')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                @if(request('date_to'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 btn-rounded bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                        To: {{ request('date_to') }}
                        <a href="{{ route('expenses.index', request()->except('date_to')) }}" class="hover:text-red-500 ml-0.5"><i class="fas fa-times text-[10px]"></i></a>
                    </span>
                @endif
                <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-primary hover:underline ml-1">
                    Clear All Filters
                </a>
            </div>
        @endif

        <!-- EXPENSES CARD COMPONENT LIST -->
        @if($expenses->count() > 0)
            <div class="space-y-4">
                <div class="flat-card bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded overflow-hidden divide-y divide-slate-100 dark:divide-slate-800/80 shadow-xs">
                    @foreach($expenses as $expense)
                        <x-transaction-card :transaction="$expense" />
                    @endforeach
                </div>

                <!-- Pagination Links -->
                <div>
                    {{ $expenses->links() }}
                </div>
            </div>
        @else
            <div class="flat-card p-8 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded">
                <x-empty-state
                    title="No expenses found"
                    description="No expenses match your current filters. Try resetting search parameters or add a new expense."
                    actionLabel="Add Expense"
                    actionEvent="open-add-expense"
                />
            </div>
        @endif

        <!-- REUSABLE FILTER MODAL COMPONENT -->
        <x-filter-modal
            :action="route('expenses.index')"
            :categories="$categories"
            :resetUrl="route('expenses.index')"
        />

        <!-- ADD & EDIT EXPENSE MODALS -->
        @include('expenses.partials.modals')

    </div>
</x-app-layout>
