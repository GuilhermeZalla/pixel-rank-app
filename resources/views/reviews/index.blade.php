<x-layout>
    <x-slot:title>PixelRank</x-slot:title>
    <x-section class="mt-10">
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
            <div class="flex flex-row gap-2 items-center">
                <input id="toggle-link" type="checkbox" class="checkbox checkbox-success h-4 w-4" />
                <label for="spoiler" class="cursor-pointer">Hide Spoiler Reviews</label>
            </div>
        </div>
        <div class="flex flex-col gap-5">
            @foreach($reviews as $review)
                <x-article-link :review="$review"></x-article-link>
            @endforeach
        </div>
        <div class="mt-5">@if(!empty($reviews->links())){{ $reviews->links() }}@endif</div>
    </x-section>
    <script>
        const toggleCheckbox = document.getElementById('toggle-link');
        const spoilerLinks = document.querySelectorAll('.review-link-spoiler');

        const hideSpoilers = localStorage.getItem('hideSpoilers') === 'true';

        toggleCheckbox.checked = hideSpoilers;

        function updateSpoilerVisibility() {
            spoilerLinks.forEach(link => {
                link.classList.toggle('hidden', toggleCheckbox.checked);
            });
        }

        toggleCheckbox.addEventListener('change', () => {
            localStorage.setItem('hideSpoilers', toggleCheckbox.checked);

            updateSpoilerVisibility();
        });

        updateSpoilerVisibility();
    </script>
</x-layout>