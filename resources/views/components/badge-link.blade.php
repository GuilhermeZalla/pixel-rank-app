@props(['active' => false, 'type' => 'badge'])

@php
    $badgeActive = 'bg-primary text-base-300 py-3';
    $default = 'badge border-[#8888884A] font-bold text-[.75rem] flex flex-row cursor-pointer';
    $style = 'text-[#FF6764] btn';

    switch ($slot) {
        case 'Recommended':
            $style = 'bg-[#033317] text-accent';
        break;
        case 'Not Recommended':
            $style = 'bg-[#280B0A] text-[#FF6764]';
            break;
        case 'Mixed':
            $style = 'bg-[#281105] text-[#EE7C37]';
            break;
        case 'Essential':
            $style = 'bg-[#001121] text-[#539AF8]';
            break;
        default:
    }

@endphp

@switch($type)
    @case('button')
        <button {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default]) }}>{{ $slot }}</button>
    @break
    @case('link')
        <a {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default . ' ' . $style]) }}>{{ $slot }}</a>
    @break
    @case('label')
        <label {{ $attributes->merge(['class' => 'p-3 gap-2 ' . $default . ' ' . $style]) }}>{{ $slot }}</label>
    @break
    @default
        <a {{ $attributes->merge(['class' => 'gap-2 ' . $default . ($active ? ' ' . $badgeActive : '')]) }}>{{ $slot }}</a>
@endswitch