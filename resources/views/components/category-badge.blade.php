@props([
    'name',
    'color' => '#2563eb',
    'icon' => 'tag'
])

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium border btn-rounded" style="background-color: {{ $color }}15; color: {{ $color }}; border-color: {{ $color }}30;">
    <i class="fas fa-{{ $icon }} text-xs"></i>
    <span>{{ $name }}</span>
</span>
