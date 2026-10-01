@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
    'icon' => null,
    'error' => null
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <div class="relative flex items-center w-full">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 dark:text-slate-500 z-10">
                <x-icon :name="$icon" class="text-xs" />
            </div>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @if($icon) style="padding-left: 2.75rem !important;" @endif
            {{ $required ? 'required' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->merge([
                'class' => 'w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border ' .
                    ($error || $errors->has($name) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-700 focus:border-primary focus:ring-primary') .
                    ' btn-rounded focus:outline-none focus:ring-1 transition-colors duration-150 disabled:bg-slate-100 dark:disabled:bg-slate-800'
            ]) }}
        />
    </div>

    @if($error || $errors->has($name))
        <p class="mt-1 text-xs text-red-500 font-medium">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
