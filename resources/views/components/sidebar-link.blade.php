@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2 rounded-md bg-white/15 text-white text-sm font-medium'
            : 'flex items-center gap-3 px-3 py-2 rounded-md text-gray-400 hover:text-white hover:bg-white/5 text-sm font-medium transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
