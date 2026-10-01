@props([
    'title' => 'No records found',
    'description' => 'There are no items to display at the moment.',
    'icon' => 'fas fa-receipt',
    'actionUrl' => null,
    'actionLabel' => null,
    'actionEvent' => null,
])

<div class="py-12 px-4 text-center border border-dashed border-slate-200 dark:border-slate-800 card-rounded">
    <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 flex items-center justify-center mb-3">
        <i class="{{ $icon }} text-xl"></i>
    </div>
    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-1">
        {{ $title }}
    </h3>
    <p class="text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-5">
        {{ $description }}
    </p>

    @if($actionUrl && $actionLabel)
        <x-button :href="$actionUrl" variant="primary" icon="fas fa-plus">
            {{ $actionLabel }}
        </x-button>
    @elseif($actionEvent && $actionLabel)
        <x-button variant="primary" icon="fas fa-plus" x-on:click="$dispatch('{{ $actionEvent }}')">
            {{ $actionLabel }}
        </x-button>
    @endif
</div>
