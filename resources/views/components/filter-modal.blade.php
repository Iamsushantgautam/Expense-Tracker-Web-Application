@props([
    'action' => '',
    'typeFilter' => null,
    'categories' => null,
    'sources' => null,
    'search' => request('search'),
    'dateFrom' => request('date_from'),
    'dateTo' => request('date_to'),
    'selectedCategory' => request('category'),
    'selectedSource' => request('source'),
    'resetUrl' => '',
])

<div
    x-show="filterModalOpen"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
>
    <div
        @click.outside="filterModalOpen = false"
        x-show="filterModalOpen"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 card-rounded max-w-md w-full p-6 shadow-2xl space-y-5"
    >
        <!-- Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-sliders"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Filter Records</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Refine timeline by keyword and parameters</p>
                </div>
            </div>
            <button type="button" @click="filterModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <!-- Modal Filter Form -->
        <form method="GET" action="{{ $action }}" class="space-y-4">
            @if($typeFilter !== null)
                <input type="hidden" name="type" value="{{ $typeFilter }}">
            @endif

            <!-- Keyword Search Input -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Keyword Search
                </label>
                <div class="relative flex items-center w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10 text-slate-400 dark:text-slate-500">
                        <x-icon name="search" class="text-xs" />
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Search title, category, or notes..."
                        style="padding-left: 2.75rem !important;"
                        class="w-full pr-3.5 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary outline-none"
                    />
                </div>
            </div>

            <!-- Category Filter Dropdown (If Provided) -->
            @if($categories && count($categories) > 0)
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Category
                    </label>
                    <select
                        name="category"
                        class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary outline-none"
                    >
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            @php $catName = is_object($cat) ? $cat->name : $cat; @endphp
                            <option value="{{ $catName }}" {{ $selectedCategory === $catName ? 'selected' : '' }}>
                                {{ $catName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Source Filter Dropdown (If Provided) -->
            @if($sources && count($sources) > 0)
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Income Source
                    </label>
                    <select
                        name="source"
                        class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary outline-none"
                    >
                        <option value="">All Sources</option>
                        @foreach($sources as $src)
                            <option value="{{ $src }}" {{ $selectedSource === $src ? 'selected' : '' }}>
                                {{ $src }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Date Range Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        From Date
                    </label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary outline-none"
                    />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        To Date
                    </label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-300 dark:border-slate-700 btn-rounded focus:border-primary outline-none"
                    />
                </div>
            </div>

            <!-- Footer Modal Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200 dark:border-slate-800">
                <a
                    href="{{ $resetUrl ?: $action }}"
                    class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-slate-200"
                >
                    Reset All
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" @click="filterModalOpen = false" class="px-4 py-2 text-xs font-semibold border border-slate-300 dark:border-slate-700 btn-rounded hover:bg-slate-100 dark:hover:bg-slate-800">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold bg-primary text-white btn-rounded shadow-md shadow-primary/20 hover:opacity-95">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
