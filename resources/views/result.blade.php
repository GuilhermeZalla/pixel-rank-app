<x-layout>
    <x-slot:title>Results for {{ request('search') }}</x-slot:title>
        <x-section class="flex flex-col gap-3">
            <h1 class="font-bold text-[1.8rem] mt-[5vh] mb-5">Result(s) for {{ request('search') }}</h1>
            <h2 class="font-bold text-[1.4rem] mb-1">Filters</h2>
            <nav class="flex flex-row justify-start gap-3">
                 <x-badge-link href="/search/popular/{{ request('search') }}"
                    :active="Str::contains(request()->path(), 'popular')">Popular</x-badge-link>
                <x-badge-link href="/search/highest-rated/{{ request('search') }}"
                    :active="request()->is('search/highest-rated/'.request('search'))">Highest Rated</x-badge-link>
                <x-badge-link href="/search/lowest-rated/{{ request('search') }}"
                    :active="request()->is('search/lowest-rated/'.request('search'))">Lowest Rated</x-badge-link>
                <x-badge-link href="/search/oldest/{{ request('search') }}"
                    :active="request()->is('search/oldest/'.request('search'))">Oldest</x-badge-link>
                <x-badge-link href="/search/hot-reviews/{{ request('search') }}"
                    :active="request()->is('search/hot-reviews/'.request('search'))">Hot</x-badge-link>
                @if(!empty($recommendationsTotal['recommended']))<x-badge-link href="/search/recommended/{{ request('search') }}"
                    :active="request()->is('search/recommended/'.request('search'))">Recommended
                <span>{{ $recommendationsTotal['recommended'] }}</span></x-badge-link>@endif
                @if(!empty($recommendationsTotal['not_recommended']))<x-badge-link
                    href="/search/not_recommended/{{ request('search') }}" :active="request()->is('search/not_recommended/'.request('search'))">Not
                    Recommended
                <span>{{ $recommendationsTotal['not_recommended'] ?? 0 }}</span></x-badge-link>@endif
                @if(!empty($recommendationsTotal['essential'])) <x-badge-link href="/search/essential/{{ request('search') }}"
                    :active="request()->is('search/essential/'.request('search'))">Essential
                <span>{{ $recommendationsTotal['essential'] ?? 0 }}</span></x-badge-link> @endif
                @if(!empty($recommendationsTotal['mixed'])) <x-badge-link href="/search/mixed/{{ request('search') }}"
                    :active="request()->is('search/mixed/'.request('search'))">Mixed
                <span>{{ $recommendationsTotal['mixed'] ?? 0 }}</span></x-badge-link>@endif
            </nav>
        </x-section>
         @if(count($reviews) === 0)
        <x-empty-result title="No results found for {{ request('search') }}" subtitle="We can't find any items matching your search."></x-empty-result>
    @else
        <x-section class="flex flex-col gap-7">
            <div class="flex flex-row justify-between gap-2 text-[.80rem] items-center">
                <h2 class="font-bold text-[1.4rem]">Reviews</h2>
                <div class="flex flex-row gap-2 items-center">
                    <input id="toggle-link" type="checkbox" class="checkbox checkbox-success h-4 w-4" />
                    <label for="spoiler" class="cursor-pointer">Hide Spoiler Reviews</label>
                </div>
            </div>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(500px,1fr))] gap-5">
                @foreach($reviews as $review)
                    <x-article-link :review="$review"></x-article-link>
                @endforeach
            </div>
            <x-pagination :pagination="$reviews"></x-pagination>

        </x-section>
    @endif
</x-layout>