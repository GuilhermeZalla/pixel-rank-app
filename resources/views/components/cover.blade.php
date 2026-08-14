@props(['type' => ''])

@php
    $default = ' opacity-80 p-2';
@endphp

<img {{ $attributes->merge(['class' => $default]) }}>
