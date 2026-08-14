@props(['type' => 'search', 'title' => '', 'subtitle' => ''])

<div class="mx-auto my-[10vh] text-center flex flex-col justify-center gap-5 items-center">
    @switch($type)
    @case('search')
    <x-heroicon-s-magnifying-glass class="text-accent size-35" />
    <h1 class="font-bold text-4xl">No results found for <span class="text-accent">{{ request('search') }}</span></h1>
    <h2 class="opacity-60 text-[1.2rem]">We can't find any items matching your search.</h2>
    @break
    @default
    @if($type === 'reviews')
            <x-heroicon-s-document-text class="text-accent size-30" />
    @else
            <x-heroicon-s-chat-bubble-oval-left-ellipsis class="text-accent size-30" />
    @endif
    <h1 class="font-bold text-2xl">{{ $title }}</h1>
    @if(!empty($subtitle))
    <h2 class="opacity-70 text-[1.2rem] -mt-4">{{ $subtitle }}</h2>
    @endif
    @endswitch
</div>
