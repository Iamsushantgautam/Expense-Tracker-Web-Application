<x-app-layout title="Category Management" active="categories">
    @php
        $totalExpenseCount = $expenseCategories->count();
        $totalIncomeCount = $incomeCategories->count();
        $totalCount = $totalExpenseCount + $totalIncomeCount;
        $customCount = $expenseCategories->where('is_default', false)->count() + $incomeCategories->where('is_default', false)->count();
    @endphp

    <div x-data="{ 
        currentTab: @js(in_array($activeTab, ['expense', 'income', 'all']) ? $activeTab : 'expense'),
        searchQuery: '',
        presetColors: ['#2563eb', '#4f46e5', '#7c3aed', '#e11d48', '#059669', '#0891b2', '#d97706', '#ec4899', '#475569'],

        // Add Modal State
        addModalOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        addCategory: {
            type: @js(old('type', 'expense')),
            name: @js(old('name', '')),
            color: @js(old('color', '#e11d48')),
            icon: @js(old('icon', 'tag'))
        },
        openAddModal(type = 'expense') {
            this.addCategory = {
                type: type,
                name: '',
                color: type === 'expense' ? '#e11d48' : '#059669',
                icon: 'tag'
            };
            this.addModalOpen = true;
        },
        closeAddModal() {
            this.addModalOpen = false;
        },

        // Edit Modal State
        editModalOpen: false,
        editCategory: {
            id: null,
            name: '',
            type: 'expense',
            color: '#2563eb',
            icon: 'tag',
            updateUrl: ''
        },
        openEditModal(cat, url) {
            this.editCategory = {
                id: cat.id,
                name: cat.name,
                type: cat.type || 'expense',
                color: cat.color || '#2563eb',
                icon: cat.icon || 'tag',
                updateUrl: url
            };
            this.editModalOpen = true;
        },
        closeEditModal() {
            this.editModalOpen = false;
        },
        matchesSearch(name) {
            if (!this.searchQuery || this.searchQuery.trim() === '') return true;
            return name.toLowerCase().includes(this.searchQuery.trim().toLowerCase());
        }
    }" class="space-y-6">

        <!-- Main Workspace Grid: Expense & Income Panels Grid (2 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

            <!-- EXPENSE CATEGORIES PANEL -->
            <div x-show="currentTab === 'expense' || currentTab === 'all'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Expense Categories</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Classifying your outgoing spending & expenses</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="openAddModal('expense')"
                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 flex items-center gap-1.5 cursor-pointer"
                        >
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Add</span>
                        </button>
                        <span class="px-2.5 py-1 text-xs font-semibold btn-rounded bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-200 dark:border-rose-900/60">
                            {{ $expenseCategories->count() }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($expenseCategories as $category)
                        <div 
                            x-show="matchesSearch('{{ addslashes($category->name) }}')"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="p-3.5 border border-slate-200/90 dark:border-slate-800/90 card-rounded bg-slate-50/60 dark:bg-slate-950/40 flex items-center justify-between"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div 
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm shrink-0" 
                                    style="background-color: {{ $category->color }};"
                                >
                                    <i class="fas fa-{{ $category->icon }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $category->name }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full" style="background-color: {{ $category->color }};"></span>
                                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
                                            {{ $category->is_default ? 'System Default' : 'Custom Category' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($category->user_id === auth()->id() || auth()->user()->is_admin)
                                    @if(!$category->is_default || auth()->user()->is_admin)
                                        <button 
                                            type="button" 
                                            @click="openEditModal({{ json_encode($category) }}, '{{ route('categories.update', $category->id) }}')"
                                            class="p-1.5 text-slate-400 rounded-lg text-xs"
                                            title="Edit Category"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if(!$category->is_default)
                                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure you want to delete category {{ addslashes($category->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-1.5 text-slate-400 rounded-lg text-xs"
                                                title="Delete Category"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if($category->is_default)
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-200/70 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        Default
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($expenseCategories->isEmpty())
                    <div class="p-8 text-center bg-slate-50/50 dark:bg-slate-950/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                        <div class="w-10 h-10 rounded-full bg-rose-500/10 text-rose-500 mx-auto flex items-center justify-center">
                            <i class="fas fa-receipt text-sm"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">No Expense Categories</h4>
                        <p class="text-[11px] text-slate-400">Add your first custom expense category.</p>
                        <button type="button" @click="openAddModal('expense')" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 cursor-pointer">
                            <i class="fas fa-plus text-[10px]"></i> Add Expense Category
                        </button>
                    </div>
                @endif
            </div>

            <!-- INCOME CATEGORIES PANEL -->
            <div x-show="currentTab === 'income' || currentTab === 'all'" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 flex items-center justify-center font-bold text-sm">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Income Categories</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Classifying your incoming earnings and revenue streams</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="openAddModal('income')"
                            class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 flex items-center gap-1.5 cursor-pointer"
                        >
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Add</span>
                        </button>
                        <span class="px-2.5 py-1 text-xs font-semibold btn-rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/60">
                            {{ $incomeCategories->count() }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($incomeCategories as $category)
                        <div 
                            x-show="matchesSearch('{{ addslashes($category->name) }}')"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="p-3.5 border border-slate-200/90 dark:border-slate-800/90 card-rounded bg-slate-50/60 dark:bg-slate-950/40 flex items-center justify-between"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div 
                                    class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm shrink-0" 
                                    style="background-color: {{ $category->color }};"
                                >
                                    <i class="fas fa-{{ $category->icon }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $category->name }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-block w-1.5 h-1.5 rounded-full" style="background-color: {{ $category->color }};"></span>
                                        <span class="text-[11px] font-medium text-slate-400 dark:text-slate-500">
                                            {{ $category->is_default ? 'System Default' : 'Custom Category' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($category->user_id === auth()->id() || auth()->user()->is_admin)
                                    @if(!$category->is_default || auth()->user()->is_admin)
                                        <button 
                                            type="button" 
                                            @click="openEditModal({{ json_encode($category) }}, '{{ route('categories.update', $category->id) }}')"
                                            class="p-1.5 text-slate-400 rounded-lg text-xs"
                                            title="Edit Category"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if(!$category->is_default)
                                        <form method="POST" action="{{ route('categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure you want to delete category {{ addslashes($category->name) }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button 
                                                type="submit" 
                                                class="p-1.5 text-slate-400 rounded-lg text-xs"
                                                title="Delete Category"
                                            >
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                                @if($category->is_default)
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-slate-200/70 text-slate-600 dark:bg-slate-800 dark:text-slate-400">
                                        Default
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($incomeCategories->isEmpty())
                    <div class="p-8 text-center bg-slate-50/50 dark:bg-slate-950/30 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 space-y-2">
                        <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-500 mx-auto flex items-center justify-center">
                            <i class="fas fa-wallet text-sm"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300">No Income Categories</h4>
                        <p class="text-[11px] text-slate-400">Add your first custom income category.</p>
                        <button type="button" @click="openAddModal('income')" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 cursor-pointer">
                            <i class="fas fa-plus text-[10px]"></i> Add Income Category
                        </button>
                    </div>
                @endif
            </div>

        </div>

        <!-- MODALS -->
        @include('categories.partials.modals')
    </div>
</x-app-layout>

