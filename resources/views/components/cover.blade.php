@props(['type' => ''])

@php
    $default = ' opacity-80 ';
@endphp

@if($type === 'cover')
    <img {{ $attributes->merge(['class' => 'rounded-bl-md rounded-tl-md aspect-video object-cover' . $default]) }}>
@else
    <img {{ $attributes->merge(['class' => $default]) }}>
@endif