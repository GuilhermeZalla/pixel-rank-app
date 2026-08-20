@props(['dashboard' => false, 'hot' => false, 'review' => ''])

@if($dashboard)
    <a href="/reviews/{{ $review->id }}" @if($review->contains_spoilers)
        class="bg-[#0E0E0E] review-link-spoiler sm:w-full md:flex-1/4 lg:flex-1/4" @else
            class="sm:w-full md:flex-1/4 lg:flex-1/4 bg-[#0E0E0E]" @endif>
            <article
                class="rounded-[10px] border-3 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
                <div class="flex flex-row p-3">
                    <x-cover class="h-[140px] rounded-[5px] object-contain" src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $review->game_cover }}.jpg"
                        alt="Review Cover"></x-cover>
                    <div class="flex flex-row justify-between w-full px-5">
                        <div class="flex flex-col justify-between flex-5/6">
                            <h2 class="font-bold text-[1rem] flex flex-row gap-3 items-center">{{ $review->title }} <span class="text-[1rem]">&middot;</span> <span class="flex flex-row items-center gap-1 opacity-50 text-[.80rem]">
                                    <x-heroicon-o-eye
                                        class="size-3.5" />{{ $review->views }} @if($review->views === 0 || $review->views === 1) view @else views @endif
                                </span></h2>
                            <p class="text-sm text-gray-400 text-[.85rem] py-2 break-all">{{ Str::limit($review->body, 250) }}
                            </p>
                            <ul class="flex flex-row gap-5 items-center text-[.80rem]">
                                <li>
                                    <x-badge-link type="label"
                                        class="rounded-[10px] px-1 py-0 font-bold text-[0.60rem]">{{ $review->recommendation->label() }}</x-badge-link>
                                </li>
                                <li class="font-bold">{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</li>
                                <li class="flex flex-row items-center gap-1"><x-heroicon-o-chat-bubble-oval-left
                                        class="size-4" />{{ count($review->comments) }} comment(s)</li>
                                @if($review->contains_spoilers)
                                    <li><span class="text-[.80rem] flex flex-row gap-2 items-center"><x-heroicon-s-exclamation-triangle
                                class="text-orange-600 size-4" />Contains Spoilers</span></li>@endif
                                <li>Review for <strong class="group-hover:text-accent transition-all duration-300 ease-in-out">{{ $review->game_name }}</strong>
                                </li>
                            </ul>
                        </div>
                        <ul class="flex flex-col justify-between items-end flex-1/6">
                            <li class="text-gray-500 text-[.85rem]">{{ $review->getPostedDate() }}</li>
                        </ul>
                    </div>
                </div>
            </article>
        </a>
@else
    <a href="/reviews/{{ $review->id }}"
    class="bg-[#0E0E0E] {{ $review->contains_spoilers ? 'review-link-spoiler' : '' }} rounded-[20px]">
        <article
            class="rounded-[20px] p-1 border-3 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
            <div class="flex flex-row justify-between h-full p-2">
                    <x-cover class="h-45 rounded-2xl object-contain" src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $review->game_cover }}.jpg" alt="Review Cover"></x-cover>
                    <div class="flex flex-col justify-start gap-2 flex-5/6 px-5">
                        <ul class="flex flex-row justify-between items-center gap-3 text-[.75rem]">
                            <li class="flex flex-row justify-center items-center gap-1 opacity-40">
                                <span class="flex flex-row items-center gap-1">
                                    <x-heroicon-o-user class="size-3" />{{ $review->user->nickname }}
                                </span> <span class="text-[1rem]">&middot;</span>
                                {{ $review->getPublishedDate() }} <span class="text-[1rem]">&middot;</span> <span class="flex flex-row items-center gap-1 text-[.75rem]">
                                    <x-heroicon-o-eye
                                        class="size-3" />{{ $review->views }} @if($review->views === 0 || $review->views === 1) view @else views @endif
                                </span>
                            </li>
                            @if($review->contains_spoilers)
                                <li><span class="flex flex-row gap-2 font-bold text-[.75rem] items-center"><x-heroicon-s-exclamation-triangle
                            class="text-orange-600 size-4" />Spoilers</span></li>@endif
                        </ul>
                        <div class="h-full">
                            <h2 class="font-bold text-[.95rem] flex flex-col gap-1"><span
                                    class="group-hover:text-accent font-normal transition-all duration-300 ease-in-out break-all wrap-break-word text-[.80rem] opacity-70">{{Str::limit($review->game_name, 190)}}</span>{{Str::limit($review->title, 150)}}
                            </h2>
                        </div>
                        <ul class="flex flex-row justify-start gap-4">
                          <li class="text-[.95rem] font-bold">{{ $review->rating === "10.0" ? 10 : $review->rating }}/10</li>
                            <li>
                                <x-badge-link type="label"
                                    class="rounded-[10px] px-2 py-0 font-bold text-[.70rem]">{{ $review->recommendation->label() }}</x-badge-link>
                            </li>
                            <li class="flex flex-row items-center gap-1 text-[.75rem]"><x-heroicon-o-chat-bubble-oval-left
                                    class="size-4" />{{ count($review->comments) }} comment(s)</li>
                        </ul>
                    </div>
            </div>
        </article>
    </a>
@endif