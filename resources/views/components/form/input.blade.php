@props(['type' => 'input'])

@if($type === 'textarea')
    <textarea {{ $attributes->merge(['class' => 'font-bold w-full border border-[#8888884A] focus:border-[#8888884A] focus:outline-none p-2.5 rounded-[5px] text-[.80rem] bg-[#0E0E0E]']) }}></textarea>
    <x-form.error name="{{ $attributes->get('bio') }}" />
@else
    <input {{ $attributes->merge(['class' => 'font-bold w-full border border-[#8888884A] focus:border-[#8888884A] focus:outline-none p-2.5 rounded-[5px] text-[.80rem] bg-[#0E0E0E]']) }} @if($attributes->get('name') !== 'password')
    value="{{ old($attributes->get('name')) }}" @endif>
    <x-form.error name="{{ $attributes->get('name') }}" />
@endif