<x-layout>
    <x-slot:title>{{ __('reviews.index_title') }}</x-slot:title>
    <x-section class="mt-10">
        <div>
            <h1 class="font-bold text-[1.7rem]">{{ __('reviews.write_review') }}</h1>
            <p class="text-[#888888ed] text-[.95rem]">{{ __('reviews.review_prompt') }}</p>
        </div>
        <form method="POST" action="/reviews/create" class="mt-5">@csrf
            <x-form.input type="textarea" name="review" rows="5" id="review" :placeholder="__('reviews.start_review_placeholder')"
                class="placeholder:text-base-content resize-none border-[#88888822] rounded-2xl" />
        </form>
    </x-section>
    <x-section class="flex flex-col gap-3">
        <h2 class="font-bold text-[1.4rem] mb-1">{{ __('reviews.filters') }}</h2>
        <nav class="flex flex-row flex-wrap justify-start gap-3">
            <x-badge-link href="/" :active="request()->is('/')">{{ __('reviews.all') }}</x-badge-link>
            <x-badge-link href="/highest-rated" :active="request()->is('highest-rated')">{{ __('reviews.highest_rated') }}</x-badge-link>
            <x-badge-link href="/lowest-rated" :active="request()->is('lowest-rated')">{{ __('reviews.lowest_rated') }}</x-badge-link>
            <x-badge-link href="/popular" :active="request()->is('popular')">{{ __('reviews.popular') }}</x-badge-link>
            <x-badge-link href="/oldest" :active="request()->is('oldest')">{{ __('reviews.oldest') }}</x-badge-link>
            <x-badge-link href="/hot-reviews" :active="request()->is('hot-reviews')">{{ __('reviews.hot') }}</x-badge-link>


            @if(!empty($recommendationsTotal['recommended']))<x-badge-link href="/recommended"
                :active="request()->is('recommended')">{{ __('reviews.recommended') }}
            <span>{{ $recommendationsTotal['recommended'] }}</span></x-badge-link>@endif
            @if(!empty($recommendationsTotal['not_recommended']))<x-badge-link
                href="/not_recommended" :active="request()->is('not_recommended')">{{ __('reviews.not_recommended') }}
            <span>{{ $recommendationsTotal['not_recommended'] ?? 0 }}</span></x-badge-link>@endif
            @if(!empty($recommendationsTotal['essential'])) <x-badge-link href="/essential"
                :active="request()->is('essential')">{{ __('reviews.essential') }}
            <span>{{ $recommendationsTotal['essential'] ?? 0 }}</span></x-badge-link> @endif
            @if(!empty($recommendationsTotal['mixed'])) <x-badge-link href="/mixed"
                :active="request()->is('mixed')">{{ __('reviews.mixed') }}
            <span>{{ $recommendationsTotal['mixed'] ?? 0 }}</span></x-badge-link>@endif


        </nav>
    </x-section>
    <x-section class="flex flex-col gap-7">
        @if(count($reviews) !== 0)
            <div class="flex flex-row justify-between gap-2 text-[.80rem] items-center">
                <h2 class="font-bold text-[1.4rem]">{{ __('reviews.reviews') }}</h2>
                <div class="flex flex-row gap-2 items-center">
                    <input id="toggle-link" type="checkbox" class="checkbox checkbox-success h-4 w-4" />
                    <label for="toggle-link" class="cursor-pointer">{{ __('reviews.hide_spoiler_reviews') }}</label>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($reviews as $review)
                    <x-article-link :review="$review"></x-article-link>
                @endforeach
            </div>
            <x-pagination :pagination="$reviews"></x-pagination>
        @else
            <div class="flex flex-col items-center mt-15 gap-3">
                <x-heroicon-s-document-text class="text-accent size-30" />
                <h1 class="font-bold text-2xl">{{ __('reviews.quiet_feed') }}</h1>
                <a href="{{ route('reviews.create') }}" class="hover:underline hover:text-accent opacity-70 text-[1.1rem] -mt-3">{{ __('reviews.first_review_prompt') }}</a>
            </div>
        @endif
    </x-section>
    <script>
        const toggleCheckbox = document.getElementById('toggle-link');
        if (toggleCheckbox) {
            const spoilerLinks = document.querySelectorAll('.review-link-spoiler');
            let hideSpoilers = false;

            try {
                hideSpoilers = localStorage.getItem('hideSpoilers') === 'true';
            } catch (error) {
                // Storage may be disabled by the browser; the filter still works for this page.
            }

            toggleCheckbox.checked = hideSpoilers;

            function updateSpoilerVisibility() {
                spoilerLinks.forEach(link => {
                    link.classList.toggle('hidden', toggleCheckbox.checked);
                });
            }

            toggleCheckbox.addEventListener('change', () => {
                try {
                    localStorage.setItem('hideSpoilers', toggleCheckbox.checked);
                } catch (error) {
                    // Keep the filter usable even when storage is unavailable.
                }

                updateSpoilerVisibility();
            });

            updateSpoilerVisibility();
        }
    </script>
</x-layout>
