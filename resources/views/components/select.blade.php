@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'disabled' => false,
    'error' => null
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
            {{ $label }}
            @if($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge([
            'class' => 'w-full px-3.5 py-2 text-sm bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 border ' .
                ($error || $errors->has($name) ? 'border-red-500 focus:ring-red-500' : 'border-slate-300 dark:border-slate-700 focus:border-primary focus:ring-primary') .
                ' btn-rounded focus:outline-none focus:ring-1 transition-colors duration-150 disabled:bg-slate-100 dark:disabled:bg-slate-800'
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $key => $val)
            @php
                $optValue = is_array($val) ? ($val['value'] ?? $key) : (is_numeric($key) ? $val : $key);
                $optLabel = is_array($val) ? ($val['label'] ?? $val) : $val;
                $isSelected = (string)$optValue === (string)old($name, $selected);
            @endphp
            <option value="{{ $optValue }}" {{ $isSelected ? 'selected' : '' }}>
                {{ $optLabel }}
            </option>
        @endforeach

        {{ $slot }}
    </select>

    @if($error || $errors->has($name))
        <p class="mt-1 text-xs text-red-500 font-medium">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
