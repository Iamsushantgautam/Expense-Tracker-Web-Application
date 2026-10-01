@props([
    'type' => 'info', // success, error, warning, info
    'message' => null
])

@php
    $typeClasses = [
        'success' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50',
        'error' => 'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-300 border-red-200 dark:border-red-800/50',
        'warning' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 border-amber-200 dark:border-amber-800/50',
        'info' => 'bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800/50',
    ][$type] ?? 'bg-blue-50 text-blue-800 dark:bg-blue-950/40 dark:text-blue-300 border-blue-200 dark:border-blue-800/50';

    $icon = [
        'success' => 'fas fa-check-circle text-emerald-500',
        'error' => 'fas fa-exclamation-circle text-red-500',
        'warning' => 'fas fa-exclamation-triangle text-amber-500',
        'info' => 'fas fa-info-circle text-blue-500',
    ][$type] ?? 'fas fa-info-circle text-blue-500';
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    class="p-4 mb-4 text-sm border btn-rounded flex items-start gap-3 {{ $typeClasses }}"
>
    <i class="{{ $icon }} text-base mt-0.5 shrink-0"></i>
    <div class="flex-1 text-sm font-medium">
        {{ $message ?? $slot }}
    </div>
    <button @click="show = false" class="text-current opacity-70 hover:opacity-100 transition-opacity">
        <i class="fas fa-times text-xs"></i>
    </button>
</div>
