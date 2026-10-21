@props(['dashboard' => false, 'hot' => false, 'review' => ''])

@if($dashboard)
    @php
        $coverUrl = !empty($review->game_cover)
            ? "https://images.igdb.com/igdb/image/upload/t_1080p/{$review->game_cover}.jpg"
            : asset('images/game-placeholder.svg');
        $commentCount = $review->comments_count ?? count($review->comments);
    @endphp
    <a href="/reviews/{{ $review->id }}" class="block w-full bg-[#0E0E0E] rounded-[10px] {{ $review->contains_spoilers ? 'review-link-spoiler' : '' }} focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">
        <article class="rounded-[10px] border border-[#88888833] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out p-3 sm:p-4">
            <div class="flex flex-row items-start gap-3 sm:gap-5">
                <x-cover class="w-20 sm:w-28 h-28 sm:h-36 shrink-0 rounded-lg object-cover" src="{{ $coverUrl }}"
                    alt="{{ __('reviews.review_cover_alt') }}"></x-cover>
                <div class="flex flex-col justify-between flex-1 min-w-0">
                    <div>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <h2 class="font-bold text-[.95rem] sm:text-[1.05rem] group-hover:text-primary transition-colors truncate">
                                {{ $review->title }}
                            </h2>
                            <span class="text-xs text-base-content/60 shrink-0 font-normal">{{ $review->getPostedDate() }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-base-content/70 mt-1">
                            <span>{{ __('reviews.review_for') }} <strong class="text-base-content group-hover:text-accent font-semibold transition-colors">{{ $review->game_name }}</strong></span>
                            <span>&middot;</span>
                            <span class="flex items-center gap-1 opacity-70">
                                <x-heroicon-o-eye class="size-3.5" />{{ trans_choice('reviews.views_count', $review->views, ['count' => $review->views]) }}
                            </span>
                        </div>
                        <p class="text-sm text-base-content/75 py-2 line-clamp-2 sm:line-clamp-3 break-words">
                            {{ Str::limit($review->body, 220) }}
                        </p>
                    </div>
                    <ul class="flex flex-row flex-wrap gap-2 sm:gap-3 items-center text-[.75rem] sm:text-[.80rem] mt-2 pt-2 border-t border-white/5">
                        <li>
                            <x-badge-link type="span"
                                class="rounded-[8px] px-2 py-0.5 font-bold text-[0.65rem]">{{ __('reviews.recommendations.' . $review->recommendation->value) }}</x-badge-link>
                        </li>
                        <li class="font-bold px-2 py-0.5 rounded bg-white/5 text-base-content/90">{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</li>
                        <li class="flex flex-row items-center gap-1 text-base-content/70"><x-heroicon-o-chat-bubble-oval-left
                                class="size-4" />{{ trans_choice('reviews.comment_count_label', $commentCount, ['count' => $commentCount]) }}</li>
                        @if($review->contains_spoilers)
                            <li>
                                <span class="flex flex-row gap-1 items-center font-semibold text-orange-500">
                                    <x-heroicon-s-exclamation-triangle class="size-3.5 shrink-0" />{{ __('reviews.contains_spoilers_short') }}
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </article>
    </a>
@else
    @php
        $commentCount = $review->comments_count ?? count($review->comments);
    @endphp
    <a href="/reviews/{{ $review->id }}"
    class="bg-[#0E0E0E] {{ $review->contains_spoilers ? 'review-link-spoiler' : '' }} rounded-[20px]">
        <article
            class="rounded-[20px] p-1 border-3 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
            <div class="flex flex-row justify-between h-full p-2">
                    <x-cover class="h-45 rounded-2xl object-contain" src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $review->game_cover }}.jpg" alt="{{ __('reviews.review_cover_alt') }}"></x-cover>
                    <div class="flex flex-col justify-start gap-2 flex-5/6 px-5">
                        <ul class="flex flex-row justify-between items-center gap-3 text-[.75rem]">
                            <li class="flex flex-row justify-center items-center gap-1 opacity-40">
                                <span class="flex flex-row items-center gap-1">
                                    <x-heroicon-o-user class="size-3" />{{ $review->user->nickname }}
                                </span> <span class="text-[1rem]">&middot;</span>
                                {{ $review->getPublishedDate() }} <span class="text-[1rem]">&middot;</span> <span class="flex flex-row items-center gap-1 text-[.75rem]">
                                    <x-heroicon-o-eye
                                        class="size-3" />{{ trans_choice('reviews.views_count', $review->views, ['count' => $review->views]) }}
                                </span>
                            </li>
                            @if($review->contains_spoilers)
                            <li><span class="flex flex-row gap-2 font-bold text-[.75rem] items-center"><x-heroicon-s-exclamation-triangle
                            class="text-orange-600 size-4" />{{ __('reviews.spoilers') }}</span></li>@endif
                        </ul>
                        <div class="h-full">
                            <h2 class="font-bold text-[.95rem] flex flex-col gap-1"><span
                                    class="group-hover:text-accent font-normal transition-all duration-300 ease-in-out break-all wrap-break-word text-[.80rem] opacity-70">{{Str::limit($review->game_name, 190)}}</span>{{Str::limit($review->title, 150)}}
                            </h2>
                        </div>
                        <ul class="flex flex-row justify-start gap-4">
                          <li class="text-[.95rem] font-bold">{{ $review->rating === "10.0" ? 10 : $review->rating }}/10</li>
                            <li>
                                <x-badge-link type="span"
                                    class="rounded-[10px] px-2 py-0 font-bold text-[.70rem]">{{ __('reviews.recommendations.' . $review->recommendation->value) }}</x-badge-link>
                            </li>
                            <li class="flex flex-row items-center gap-1 text-[.75rem]"><x-heroicon-o-chat-bubble-oval-left
                                    class="size-4" />{{ trans_choice('reviews.comment_count_label', $commentCount, ['count' => $commentCount]) }}</li>
                        </ul>
                    </div>
            </div>
        </article>
    </a>
@endif
