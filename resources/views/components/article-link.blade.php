@props(['dashboard' => false, 'hot' => false, 'review' => ''])

@if($dashboard)
    <a href="/reviews/{{ $review->id }}" @if($review->contains_spoilers)
        class="bg-[#0E0E0E] review-link-spoiler sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px]" @else
            class="sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px] bg-[#0E0E0E]" @endif>
            <article
                class="rounded-[7px] border-2 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
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
@elseif($hot)
    <a href="/reviews/{{ $review->id }}" @if($review->contains_spoilers)
    class="bg-[#0E0E0E] review-link-spoiler sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[20px]" @else
        class="sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[20px] bg-[#0E0E0E]" @endif>
        <article
            class="rounded-[20px] border-2 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
            <div class="flex flex-row gap-1 rounded-[20px]">
                <x-cover type="cover" class="h-[110px] w-[140px]"
                    src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmbdujr8xhGqABB81Nt4VjhM8a_GlRee7rI6MjmNr_0n_gH4uEuMIUuy3p&s=10"
                    alt="Review Cover"></x-cover>
                <div class="p-3 flex flex-row justify-between w-full pr-5">
                    <div class="flex flex-col justify-between flex-5/6">
                        <h2 class="font-bold text-[.90rem] break-all">{{ $review->title }}</h2>
                        <ul class="flex flex-row gap-3 items-center text-[.80rem]">
                            <li class="text-[.75rem]">Review for <strong
                                    class="group-hover:text-accent transition-all duration-300 ease-in-out">{{ $review->game_name }}</strong>
                            </li>
                        </ul>
                        <span class="text-gray-500 text-[.75rem] self-end">{{ $review->getPostedDate() }}</span>
                    </div>
                </div>
            </div>
        </article>
    </a>
@else
    <a href="/reviews/{{ $review->id }}" @if($review->contains_spoilers)
    class="bg-[#0E0E0E] review-link-spoiler sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px]" @else
        class="sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px] bg-[#0E0E0E]" @endif>
        <article
            class="rounded-[7px] border-2 border-[#88888822] group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
            <div class="flex flex-row gap-3">
                    <x-cover type="cover" class="h-[150px] w-[220px]"
                        src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmbdujr8xhGqABB81Nt4VjhM8a_GlRee7rI6MjmNr_0n_gH4uEuMIUuy3p&s=10"
                        alt="Review Cover"></x-cover>
                <div class="px-5 py-3 flex flex-row justify-between w-full">
                    <div class="flex flex-col justify-between flex-5/6">
                        <h2 class="font-bold text-[1.2rem] pb-2">{{ $review->title }}</h2>
                        <p class="text-sm text-gray-400 text-[.90rem] py-2 break-all">{{ Str::limit($review->body, 150) }}
                        </p>
                        <ul class="flex flex-row gap-5 items-center text-[.85rem] mt-2">
                            <li>
                                <x-badge-link type="label"
                                    class="rounded-[10px] px-1 py-0 font-bold text-[0.70rem]">{{ $review->recommendation->label() }}</x-badge-link>
                            </li>
                            <li class="font-bold">{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</li>
                            <li class="flex flex-row items-center gap-1"><x-heroicon-o-chat-bubble-oval-left
                                    class="size-4" />{{ count($review->comments) }} comment(s)</li>
                            @if($review->contains_spoilers)
                                <li><span class="text-[.80rem] flex flex-row gap-2"><x-heroicon-s-exclamation-triangle
                            class="text-orange-600 size-4" />Contains Spoilers</span></li>@endif
                            <li class="text-[.80rem]">Review for <strong
                                    class="group-hover:text-accent transition-all duration-300 ease-in-out">{{ $review->game_name }}</strong>
                            </li>
                        </ul>
                    </div>
                    <ul class="flex flex-col justify-between items-end flex-1/6">
                        <li
                            class="text-[.82rem] flex flex-row items-center gap-1 font-bold group-hover:text-accent transition-all duration-300 ease-in-out">
                            <x-heroicon-o-user class="size-3.5" />{{ $review->user->nickname }}
                        </li>
                        <li class="text-gray-500 text-[.82rem]">{{ $review->getPostedDate() }}</li>
                    </ul>
                </div>
            </div>
        </article>
    </a>
@endif