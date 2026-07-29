<a href="/reviews/{{ $review->id }}" class="sm:w-full md:flex-1/4 lg:flex-1/4 rounded-[7px]">
    <article class="rounded-[7px] border-2 border-[#88888822] group hover:border-primary transition-all duration-300 ease-in-out">
        <div class="flex flex-row gap-3">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmbdujr8xhGqABB81Nt4VjhM8a_GlRee7rI6MjmNr_0n_gH4uEuMIUuy3p&s=10"
                alt="Placeholder Image" class="rounded-bl-md rounded-tl-md h-42 w-65 object-cover">
            <div class="py-4 px-3 flex flex-row justify-between w-full">
                <div class="flex flex-col justify-between">
                    <h2 class="font-bold text-[1.4rem] pb-2">{{ $review->title }}</h2>
                    <p class="text-sm text-gray-400 text-[.90rem]">{{ Str::limit($review->body, 350) }}</p>
                    <ul class="flex flex-row gap-5 items-center text-[.85rem] mt-2">
                        <li>
                            <x-badge-link type="label"
                                class="rounded-[10px] px-1 py-0 font-bold text-[0.70rem]">{{ $review->recommendation->label() }}</x-badge-link>
                        </li>
                        <li class="font-bold">{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</li>
                        <li class="flex flex-row items-center gap-1"><x-heroicon-o-chat-bubble-oval-left
                                class="size-4" />{{ count($review->comments) }} comment(s)</li>
                    </ul>
                </div>
                <ul class="flex flex-col justify-between">
                    <li class="text-[.82rem] flex flex-row items-center gap-1 font-bold group-hover:text-accent">
                        <x-heroicon-o-user class="size-3.5" /> Guilherme</li>
                    <li class="text-gray-500 text-[.82rem]">2 hours ago</li>
                </ul>
            </div>
        </div>
    </article>
</a>