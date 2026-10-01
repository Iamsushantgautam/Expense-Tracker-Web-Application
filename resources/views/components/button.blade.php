@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => null,
    'disabled' => false,
    'loadingText' => null,
])

@php
    $baseClasses = 'relative inline-flex items-center justify-center font-medium btn-rounded transition-all duration-150 focus:outline-none disabled:opacity-75 disabled:cursor-not-allowed select-none cursor-pointer';

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2 text-sm gap-2',
        'lg' => 'px-5 py-2.5 text-base gap-2.5',
    ][$size] ?? 'px-4 py-2 text-sm gap-2';

    $variantClasses = [
        'primary' => 'bg-primary text-white bg-primary-hover border border-transparent shadow-xs',
        'secondary' => 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 border border-transparent shadow-xs',
        'outline' => 'bg-transparent text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800',
        'ghost' => 'bg-transparent text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 border border-transparent',
    ][$variant] ?? 'bg-primary text-white bg-primary-hover border border-transparent';

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a
        href="{{ $href }}"
        x-data="{ loading: false }"
        @click="if(!loading) { loading = true; } else { $event.preventDefault(); }"
        :class="{ 'opacity-75 pointer-events-none': loading }"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        <!-- Loading State -->
        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
            <i class="fas fa-circle-notch fa-spin text-current text-xs"></i>
            <span>{{ $loadingText ?? 'Loading...' }}</span>
        </span>

        <!-- Default State -->
        <span x-show="!loading" class="inline-flex items-center gap-2">
            @if($icon)
                <i class="{{ $icon }} text-current"></i>
            @endif
            <span>{{ $slot }}</span>
        </span>
    </a>
@else
    <button
        type="{{ $type }}"
        x-data="{ loading: false }"
        @click="
            if (!loading && ($el.type !== 'submit' || !$el.form || $el.form.checkValidity())) {
                setTimeout(() => { loading = true; }, 20);
            }
        "
        :disabled="loading || {{ $disabled ? 'true' : 'false' }}"
        :class="{ 'opacity-75 cursor-not-allowed': loading }"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        <!-- Loading State -->
        <span x-show="loading" x-cloak class="inline-flex items-center gap-2">
            <i class="fas fa-circle-notch fa-spin text-current text-xs"></i>
            <span>{{ $loadingText ?? 'Processing...' }}</span>
        </span>

        <!-- Default State -->
        <span x-show="!loading" class="inline-flex items-center gap-2">
            @if($icon)
                <i class="{{ $icon }} text-current"></i>
            @endif
            <span>{{ $slot }}</span>
        </span>
    </button>
@endif
