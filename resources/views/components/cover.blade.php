@props(['type' => ''])

@php
    $default = ' opacity-80';
@endphp

<img {{ $attributes->merge(['class' => $default]) }}>
