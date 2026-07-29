@props(['type' => 'input'])

@php
    $default = ' font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E]';
@endphp

@if($type === 'textarea')
    <textarea {{ $attributes->merge(['class' => 'text-[.85rem] resize-none p-3' . $default]) }}></textarea>
    <x-form.error name="{{ $attributes->get('bio') }}" />
@elseif($type === 'checkbox')
    <input type="text" {{ $attributes->merge(['class' => 'text-[.80rem] hover:border-accent readonly cursor-pointer flex-1/3 text-center p-2.5' . $default]) }} placeholder="{{ $attributes->get('placeholder') }}"/>
@else
    <input {{ $attributes->merge(['class' => 'p-2.5 text-[.80rem]' . $default]) }} @if($attributes->get('name') !== 'password')
    value="{{ old($attributes->get('name')) }}" @endif>
    <x-form.error name="{{ $attributes->get('name') }}" />
@endif