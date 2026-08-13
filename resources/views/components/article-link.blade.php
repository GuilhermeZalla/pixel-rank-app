@props(['dashboard' => false, 'hot' => false, 'review' => ''])

@if($dashboard)
    <a href="/reviews/{{ $review->id }}" @if($review->contains_spoilers)
        class="bg-[#0E0E0E] review-link-spoiler sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px]" @else
            class="sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px] bg-[#0E0E0E]" @endif>
            <article
                class="rounded-[7px] border-3 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
                <div class="flex flex-row gap-3">
                    <x-cover type="cover" class="h-[120px] w-[200px]" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmbdujr8xhGqABB81Nt4VjhM8a_GlRee7rI6MjmNr_0n_gH4uEuMIUuy3p&s=10"
                        alt="Review Cover"></x-cover>
                    <div class="py-4 px-3 flex flex-row justify-between w-full">
                        <div class="flex flex-col justify-between flex-5/6">
                            <h2 class="font-bold text-[1rem]">{{ $review->title }}</h2>
                            <p class="text-sm text-gray-400 text-[.85rem] py-2 break-all">{{ Str::limit($review->body, 120) }}
                            </p>
                            <ul class="flex flex-row gap-3 items-center text-[.80rem]">
                                <li>
                                    <x-badge-link type="label"
                                        class="rounded-[10px] px-1 py-0 font-bold text-[0.58rem]">{{ $review->recommendation->label() }}</x-badge-link>
                                </li>
                                <li class="font-bold">{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</li>
                                <li class="flex flex-row items-center gap-1"><x-heroicon-o-chat-bubble-oval-left
                                        class="size-4" />{{ count($review->comments) }} comment(s)</li>
                                @if($review->contains_spoilers)
                                    <li><span class="text-[.80rem] flex flex-row gap-2"><x-heroicon-s-exclamation-triangle
                                class="text-orange-600 size-4" />Contains Spoilers</span></li>@endif
                                <li class="text-[.75rem]">Review for <strong class="group-hover:text-accent transition-all duration-300 ease-in-out">{{ $review->game_name }}</strong>
                                </li>
                            </ul>
                        </div>
                        <ul class="flex flex-col justify-between items-end flex-1/6">
                            <li
                                class="text-[.75rem] flex flex-row items-center gap-1 font-bold group-hover:text-accent transition-all duration-300 ease-in-out">
                                <x-heroicon-o-user class="size-3" />{{ $review->user->nickname }}
                            </li>
                            <li class="text-gray-500 text-[.82rem]">{{ $review->getPostedDate() }}</li>
                        </ul>
                    </div>
                </div>
            </article>
        </a>
@else
    <a href="/reviews/{{ $review->id }}"
    class="bg-[#0E0E0E] {{ $review->contains_spoilers ? 'review-link-spoiler' : '' }} rounded-[7px] h-40">
        <article
            class="rounded-[7px] h-full border-3 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
            <div class="flex flex-row justify-between h-full rounded-[7px]">
                <x-cover class="h-full w-[200px] object-cover p-2 rounded-2xl"
                    src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $review->game_cover }}.jpg" alt="Review Cover"></x-cover>
                    <div class="flex flex-col justify-start gap-3 flex-5/6 px-4 py-3">
                        <ul class="flex flex-row justify-between items-center gap-3 text-[.65rem]">
                            <li class="flex flex-row justify-center items-center gap-1 opacity-60">
                                <x-heroicon-o-user class="size-3" />{{ $review->user->nickname }} <span
                                    class="text-[1rem]">&middot;</span> {{ $review->getPublishedDate() }}
                            </li>
                            @if($review->contains_spoilers)
                                <li><span class="flex flex-row gap-2 font-bold text-[.75rem] items-center"><x-heroicon-s-exclamation-triangle
                            class="text-orange-600 size-4" />Spoilers</span></li>@endif
                        </ul>
                        <h2 class="font-bold text-[.95rem] flex flex-col gap-1 h-full"><span
                                class="group-hover:text-accent font-normal transition-all duration-300 ease-in-out break-all wrap-break-word text-[.75rem] opacity-70">{{Str::limit($review->game_name, 190)}}</span>{{Str::limit($review->title, 150)}}
                        </h2>
                        <ul class="flex flex-row justify-start gap-4">
                             <li class="text-[.95rem] font-bold">{{ $review->rating }}/10</li>
                            <li>
                                <x-badge-link type="label"
                                    class="rounded-[10px] px-2 py-0 font-bold text-[0.65rem]">{{ $review->recommendation->label() }}</x-badge-link>
                            </li>
                            <li class="flex flex-row items-center gap-1 text-[.75rem]"><x-heroicon-o-chat-bubble-oval-left
                                    class="size-4" />{{ count($review->comments) }} comment(s)</li>
                        </ul>
                    </div>
            </div>
        </article>
    </a>
@endif