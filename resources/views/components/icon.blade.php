@props([
    'name' => 'circle',
    'class' => '',
    'size' => null
])

@php
    $iconName = trim($name);
    // Automatically prepend 'fas fa-' if only icon name (e.g. 'search', 'plus') is passed
    if (!str_starts_with($iconName, 'fa-') && !str_starts_with($iconName, 'fas ') && !str_starts_with($iconName, 'far ') && !str_starts_with($iconName, 'fab ')) {
        $iconName = 'fas fa-' . $iconName;
    } elseif (str_starts_with($iconName, 'fa-') && !str_contains($iconName, ' ')) {
        $iconName = 'fas ' . $iconName;
    }

    if ($size) {
        $iconName .= ' fa-' . $size;
    }
@endphp

<i {{ $attributes->merge(['class' => trim($iconName . ' ' . $class)]) }}></i>
