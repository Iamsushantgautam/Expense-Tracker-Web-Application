@props([
    'action' => '',
    'name' => 'search',
    'value' => request('search'),
    'placeholder' => 'Search title, category, or notes...',
    'hiddenFields' => []
])

<form method="GET" action="{{ $action }}" class="flex-1 max-w-md relative">
    @foreach($hiddenFields as $key => $val)
        @if($val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endif
    @endforeach

    <div class="relative flex items-center w-full">
        <!-- Search Magnifying Glass Icon -->
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none z-10 text-slate-400 dark:text-slate-500">
            <x-icon name="search" class="text-xs" />
        </div>

        <!-- Search Input Field with Guaranteed Left Padding (2.75rem / 44px) -->
        <input
            type="text"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            style="padding-left: 2.75rem !important;"
            class="w-full pr-9 py-2 text-xs bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 border border-slate-200 dark:border-slate-800 btn-rounded focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-colors"
        />

        <!-- Clear Search Action Button -->
        @if($value)
            <a
                href="{{ $action ?: url()->current() }}"
                class="absolute right-3 inset-y-0 my-auto h-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs flex items-center justify-center p-0.5 z-10"
                title="Clear Search"
            >
                <x-icon name="times-circle" />
            </a>
        @endif
    </div>
</form>
