<x-layout>
    <x-slot:title>{{ __('reviews.search_title', ['search' => request('search')]) }}</x-slot:title>
        <x-section class="flex flex-col gap-3">
            <h1 class="font-bold text-[1.8rem] mt-[5vh] mb-5">{{ __('reviews.search_heading', ['search' => request('search')]) }}</h1>
            <h2 class="font-bold text-[1.4rem] mb-1">{{ __('reviews.filters') }}</h2>
            <nav class="flex flex-row justify-start gap-3">
                 <x-badge-link href="/search/popular/{{ request('search') }}"
                    :active="Str::contains(request()->path(), 'popular')">{{ __('reviews.popular') }}</x-badge-link>
                <x-badge-link href="/search/highest-rated/{{ request('search') }}"
                    :active="request()->is('search/highest-rated/'.request('search'))">{{ __('reviews.highest_rated') }}</x-badge-link>
                <x-badge-link href="/search/lowest-rated/{{ request('search') }}"
                    :active="request()->is('search/lowest-rated/'.request('search'))">{{ __('reviews.lowest_rated') }}</x-badge-link>
                <x-badge-link href="/search/oldest/{{ request('search') }}"
                    :active="request()->is('search/oldest/'.request('search'))">{{ __('reviews.oldest') }}</x-badge-link>
                <x-badge-link href="/search/hot-reviews/{{ request('search') }}"
                    :active="request()->is('search/hot-reviews/'.request('search'))">{{ __('reviews.hot') }}</x-badge-link>
                @if(!empty($recommendationsTotal['recommended']))<x-badge-link href="/search/recommended/{{ request('search') }}"
                    :active="request()->is('search/recommended/'.request('search'))">{{ __('reviews.recommended') }}
                <span>{{ $recommendationsTotal['recommended'] }}</span></x-badge-link>@endif
                @if(!empty($recommendationsTotal['not_recommended']))<x-badge-link
                    href="/search/not_recommended/{{ request('search') }}" :active="request()->is('search/not_recommended/'.request('search'))">{{ __('reviews.not_recommended') }}
                <span>{{ $recommendationsTotal['not_recommended'] ?? 0 }}</span></x-badge-link>@endif
                @if(!empty($recommendationsTotal['essential'])) <x-badge-link href="/search/essential/{{ request('search') }}"
                    :active="request()->is('search/essential/'.request('search'))">{{ __('reviews.essential') }}
                <span>{{ $recommendationsTotal['essential'] ?? 0 }}</span></x-badge-link> @endif
                @if(!empty($recommendationsTotal['mixed'])) <x-badge-link href="/search/mixed/{{ request('search') }}"
                    :active="request()->is('search/mixed/'.request('search'))">{{ __('reviews.mixed') }}
                <span>{{ $recommendationsTotal['mixed'] ?? 0 }}</span></x-badge-link>@endif
            </nav>
        </x-section>
         @if(count($reviews) === 0)
        <x-empty-result :title="__('reviews.no_results_title', ['search' => request('search')])" :subtitle="__('reviews.no_results_description')"></x-empty-result>
    @else
        <x-section class="flex flex-col gap-7">
            <div class="flex flex-row justify-between gap-2 text-[.80rem] items-center">
                <h2 class="font-bold text-[1.4rem]">{{ __('reviews.reviews') }}</h2>
                <div class="flex flex-row gap-2 items-center">
                    <input id="toggle-link" type="checkbox" class="checkbox checkbox-success h-4 w-4" />
                    <label for="spoiler" class="cursor-pointer">{{ __('reviews.hide_spoiler_reviews') }}</label>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($reviews as $review)
                    <x-article-link :review="$review"></x-article-link>
                @endforeach
            </div>
            <x-pagination :pagination="$reviews"></x-pagination>

        </x-section>
    @endif
</x-layout>
