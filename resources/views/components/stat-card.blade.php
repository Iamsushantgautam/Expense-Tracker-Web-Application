@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'neutral',
    'icon' => null,
    'subtitle' => null
])

<div {{ $attributes->merge(['class' => 'flat-card p-5']) }}>
    <div class="flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            {{ $title }}
        </span>
        @if($icon)
            <div class="w-8 h-8 rounded flex items-center justify-center bg-primary-light text-primary">
                <i class="{{ $icon }} text-base"></i>
            </div>
        @endif
    </div>

    <div class="mt-2 flex items-baseline justify-between">
        <div class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
            {{ $value }}
        </div>
        @if($change !== null)
            <div class="inline-flex items-center text-xs font-semibold px-2 py-0.5 btn-rounded
                {{ $changeType === 'positive' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : '' }}
                {{ $changeType === 'negative' ? 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-400' : '' }}
                {{ $changeType === 'neutral' ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' : '' }}
            ">
                @if($changeType === 'positive')
                    <i class="fas fa-arrow-down mr-1"></i>
                @elseif($changeType === 'negative')
                    <i class="fas fa-arrow-up mr-1"></i>
                @endif
                {{ $change }}
            </div>
        @endif
    </div>

    @if($subtitle)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            {{ $subtitle }}
        </p>
    @endif
</div>
