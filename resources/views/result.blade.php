<x-layout>
    <x-slot:title>Results for Laravel</x-slot:title>
    @if(count($reviews) === 0)
        <x-search-error></x-search-error>
    @else
    <x-section class="flex flex-col gap-3">
        <h1 class="font-bold text-[1.8rem] mt-[5vh] mb-5">Result(s) for {{ request('search') }}</h1>
        <h2 class="font-bold text-[1.4rem] mb-1">Filters</h2>
        <nav class="flex flex-row justify-start gap-3">
            <x-badge-link href="/search" :active="request()->is('/search')">All</x-badge-link>
            <x-badge-link href="/search/highest-rated" :active="request()->is('search/highest-rated')">Highest Rated</x-badge-link>
            <x-badge-link href="/search/lowest-rated" :active="request()->is('search/lowest-rated')">Lowest Rated</x-badge-link>
            <x-badge-link href="/search/popular" :active="request()->is('search/popular')">Popular</x-badge-link>
            <x-badge-link href="/search/oldest" :active="request()->is('search/oldest')">Oldest</x-badge-link>
            <x-badge-link href="/search/hot-reviews" :active="request()->is('search/hot-reviews')">Hot</x-badge-link>
            <x-badge-link href="/search/recommended" :active="request()->is('search/recommended')">Recommended
                <span>{{ $recommendationsTotal['recommended'] }}</span></x-badge-link>
            <x-badge-link href="/search/not_recommended" :active="request()->is('search/not_recommended')">Not Recommended
                <span>{{ $recommendationsTotal['not_recommended'] }}</span></x-badge-link>
            <x-badge-link href="/search/essential" :active="request()->is('search/essential')">Essential
                <span>{{ $recommendationsTotal['essential'] }}</span></x-badge-link>
            <x-badge-link href="/search/mixed" :active="request()->is('search/mixed')">Mixed
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
         <div class="grid gap-5 grid-cols-[repeat(auto-fill,minmax(260px,1fr))]">
            @foreach($reviews as $review)
                <x-article-link :review="$review"></x-article-link>
            @endforeach
        </div>
        <div class="mt-5">@if(!empty($reviews->links())){{ $reviews->links() }}@endif</div>
    </x-section>
    @endif
</x-layout>