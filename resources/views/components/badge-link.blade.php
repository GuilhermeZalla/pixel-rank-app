@props(['active' => false, 'type' => 'badge'])

@php
    $badgeActive = 'bg-primary text-base-300 py-3';
    $default = 'badge border-[#8888884A] font-bold text-[.75rem] flex flex-row';
@endphp

@switch($type)
    @case('button')
        <button {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default]) }}>{{ $slot }}</button>
    @break
    @case('link')
        <a {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default]) }}>{{ $slot }}</a>
    @break
    @case('label')
        <label {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default]) }}>{{ $slot }}</label>
    @break
    @default
        <a {{ $attributes->merge(['class' => 'gap-4 ' . $default . ($active ? ' ' . $badgeActive : '')]) }}>{{ $slot }}</a>
@endswitch