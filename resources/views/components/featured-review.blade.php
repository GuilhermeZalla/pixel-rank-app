@props(['review', 'featured' => false])

@php
    $coverUrl = !empty($review->game_cover)
        ? 'https://images.igdb.com/igdb/image/upload/t_1080p/' . $review->game_cover . '.jpg'
        : asset('images/game-placeholder.svg');
    $rating = $review->rating === '10.0' ? 10 : $review->rating;
    $spoilerClass = $review->contains_spoilers ? 'review-link-spoiler' : '';
@endphp

@if($featured)
    <a href="{{ url('/reviews/' . $review->id) }}"
        class="group relative isolate block min-h-72 overflow-hidden rounded-2xl border border-base-content/10 bg-base-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent sm:min-h-[22rem] lg:min-h-[27rem] {{ $spoilerClass }}">
        <x-cover class="absolute inset-0 -z-10 size-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ $coverUrl }}" alt="" />
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/90 via-black/15 to-black/5"></div>
        <span class="absolute right-4 top-4 rounded-lg bg-black/70 px-3 py-1.5 text-sm font-bold text-white backdrop-blur-sm">{{ $rating }}/10</span>
        <div class="absolute inset-x-0 bottom-0 p-4 sm:p-6">
            <p class="mb-1 line-clamp-1 text-sm font-medium text-white/80">{{ $review->game_name }}</p>
            <h2 class="line-clamp-2 text-xl font-bold text-white sm:text-2xl">{{ $review->title }}</h2>
            @if($review->contains_spoilers)
                <span class="mt-2 inline-flex items-center gap-1 rounded bg-black/60 px-2 py-1 text-xs font-semibold text-orange-300">
                    <x-heroicon-s-exclamation-triangle class="size-3" />{{ __('reviews.contains_spoilers_short') }}
                </span>
            @endif
        </div>
    </a>
@else
    <a href="{{ url('/reviews/' . $review->id) }}"
        class="group flex min-w-0 overflow-hidden rounded-xl border border-base-content/10 bg-base-300 transition-colors hover:border-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-accent {{ $spoilerClass }}">
        <x-cover class="h-24 w-24 shrink-0 object-cover sm:h-28 sm:w-28" src="{{ $coverUrl }}" alt="" />
        <div class="flex min-w-0 flex-1 flex-col justify-center gap-1.5 p-3">
            <p class="line-clamp-1 text-xs text-base-content/65">{{ $review->game_name }}</p>
            <h2 class="line-clamp-2 text-sm font-bold leading-snug transition-colors group-hover:text-primary">{{ $review->title }}</h2>
            <span class="text-xs font-bold text-primary">{{ $rating }}/10</span>
        </div>
        @if($review->contains_spoilers)
            <span class="flex shrink-0 items-start p-2 text-orange-500 text-[.70rem] gap-1 m-2" title="{{ __('reviews.contains_spoilers_short') }}">
                <x-heroicon-s-exclamation-triangle class="size-4" /> Spoilers
            </span>
        @endif
    </a>
@endif
