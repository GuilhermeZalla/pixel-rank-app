<x-layout>
    <x-slot:title>PixelRank</x-slot:title>
    <x-section class="flex flex-row gap-8 mt-15 p-3">
        <div x-data="carousel" class="relative w-[70%] rounded-[20px] h-96 overflow-hidden" @mouseenter="stopAutoplay()"
            @mouseleave="startAutoplay()">
            <div class="flex h-full" :class="transitionEnabled ? 'transition-transform duration-700 ease-in-out' : ''"
                :style="`transform: translateX(-${current * 100}%)`" @transitionend="handleTransitionEnd()">
                <template x-for="(slide, index) in extendedSlides" :key="index">
                    <div class="w-full h-full flex-shrink-0 px-2">
                        <a href=""
                            class="relative block w-full h-full rounded-[20px] border-2 border-transparent group hover:border-accent overflow-hidden">
                            <img :src="`https://images.igdb.com/igdb/image/upload/t_1080p/${slide.src}.jpg`"
                                class="w-full h-full object-cover aspect-auto rounded-[20px]">
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent rounded-[20px]">
                            </div>
                            <div class="absolute bottom-6 left-6 right-6 text-white">
                                <h3 x-text="slide.game" class="group-hover:text-accent font-bold text-[.95rem]"></h3>
                                <h2 class="text-3xl font-bold mb-3" x-text="slide.title"></h2>
                                <p class="text-md mt-1 opacity-90 break-all" x-text="truncate(slide.subtitle)"></p>
                            </div>
                        </a>
                    </div>
                </template>
            </div>
            <button x-on:click="previous(); restartAutoplay()"
                class="cursor-pointer absolute left-5 top-1/2 hover:bg-secondary/70 -translate-y-1/2 bg-black/50 text-white rounded-full w-10 h-10 flex items-center justify-center ">
                &#10094;
            </button>
            <button x-on:click="next(); restartAutoplay()"
                class="cursor-pointer absolute right-5 top-1/2 -translate-y-1/2 bg-black/50 text-white rounded-full w-10 h-10 flex items-center justify-center hover:bg-secondary/70">
                &#10095;
            </button>
        </div>
        @if(!empty($reviewsHot))
            <div class="flex flex-col gap-6 w-[30%]">
                @foreach($reviewsHot as $review)
                    <x-article-link :review="$review" hot="true"></x-article-link>
                @endforeach
            </div>
        @endif
    </x-section>
    <x-section class="-mt-15">
        <div>
            <h1 class="font-bold text-[1.7rem]">Write your review!</h1>
            <p class="text-[#888888ed] text-[.95rem]">Capture your thoughts and experiences with a game.</p>
        </div>
        <form method="POST" action="/reviews/create" class="mt-5">@csrf
            <x-form.input type="textarea" name="review" rows="5" id="review" placeholder="What's your review?"
                class="placeholder:text-base-content resize-none border-[#88888822]" />
        </form>
    </x-section>
    <x-section class="flex flex-col gap-3">
        <h2 class="font-bold text-[1.4rem] mb-1">Filters</h2>
        <nav class="flex flex-row justify-start gap-3">
            <x-badge-link href="/" :active="request()->is('/')">All</x-badge-link>
            <x-badge-link href="/highest-rated" :active="request()->is('highest-rated')">Highest Rated</x-badge-link>
            <x-badge-link href="/lowest-rated" :active="request()->is('lowest-rated')">Lowest Rated</x-badge-link>
            <x-badge-link href="/popular" :active="request()->is('popular')">Popular</x-badge-link>
            <x-badge-link href="/oldest" :active="request()->is('oldest')">Oldest</x-badge-link>
            <x-badge-link href="/hot-reviews" :active="request()->is('hot-reviews')">Hot</x-badge-link>
            <x-badge-link href="/recommended" :active="request()->is('recommended')">Recommended
                <span>{{ $recommendationsTotal['recommended'] }}</span></x-badge-link>
            <x-badge-link href="/not_recommended" :active="request()->is('not_recommended')">Not Recommended
                <span>{{ $recommendationsTotal['not_recommended'] }}</span></x-badge-link>
            <x-badge-link href="/essential" :active="request()->is('essential')">Essential
                <span>{{ $recommendationsTotal['essential'] }}</span></x-badge-link>
            <x-badge-link href="/mixed" :active="request()->is('mixed')">Mixed
                <span>{{ $recommendationsTotal['mixed'] }}</span></x-badge-link>
        </nav>
    </x-section>
    <x-section class="flex flex-col gap-7">
        <div class="flex flex-row justify-between gap-2 text-[.80rem] items-center">
            <h2 class="font-bold text-[1.4rem]">Reviews</h2>
            <div class="flex flex-row gap-2">
                <input id="toggle-link" type="checkbox" class="checkbox checkbox-success h-4 w-4" />
                <label for="spoiler" class="cursor-pointer">Hide Spoiler Reviews</label>
            </div>
        </div>
        @foreach($reviews as $review)
            <x-article-link :review="$review"></x-article-link>
        @endforeach
        <div class="mt-5">@if(!empty($reviews->links())){{ $reviews->links() }}@endif</div>
    </x-section>
    <script>
        const toggleCheckbox = document.getElementById('toggle-link');
        const spoilerLinks = document.querySelectorAll('.review-link-spoiler');
        const reviews = @json($reviewsHighest);
        const covers = @json($reviewsCovers);
        let banners = [];

        for ($i = 0; $i < reviews.length; $i++) {
            banners.push(reviews[$i]);
        }

        function updateSpoilerVisibility() {
            spoilerLinks.forEach(link => {
                link.classList.toggle('hidden', toggleCheckbox.checked);
            });
        }

        toggleCheckbox.addEventListener('change', updateSpoilerVisibility);

        updateSpoilerVisibility();

    </script>
</x-layout>