@props(['type' => 'input'])

@php
$default = ' font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E] ';
@endphp

@switch($type)
    @case('textarea')
        <textarea {{ $attributes->merge(['class' => 'text-[.85rem] resize-none p-3 ' . $default]) }} @if(!empty($attributes->get('value'))) value="{{ old($attributes->get('value')) }}" @endif>{{ $attributes->get('value') }}</textarea>
        <x-form.error name="{{ $attributes->get('bio') }}" />
        @break

    @case('search')
        <input type="text" id="game-search" placeholder="Search a game..." autocomplete="on" {{ $attributes->merge(['class' => 'p-2.5 text-[.80rem] ' . $default]) }} required>
        <input type="hidden" name="game_id" id="game-id">
        <input type="hidden" name="game_name" id="game-name">
        <input type="hidden" name="game_cover" id="game-cover">

        <div id="game-dropdown" class="{{ 'absolute top-20 z-99 flex flex-col gap-3 hidden overflow-y-scroll max-h-80 ' . $default }}"> </div>
        @break

    @case('checkbox')
        <input type="checkbox" checked="checked" {{ $attributes->merge(['class' => 'checkbox checkbox-success']) }} value="1" />
        @break

    @default
        <input {{ $attributes->merge(['class' => 'p-2.5 text-[.80rem]' . $default]) }} @if($attributes->get('name') !== 'password') value="{{ old($attributes->get('name')) }}" @endif type="{{ $type }}" />
        <x-form.error name="{{ $attributes->get('name') }}" />
@endswitch