<x-layout>
    <x-slot:title>PixelRank</x-slot:title>
    <x-section class="flex flex-col px-40">
        <div class="mt-10">
            <h1 class="font-bold text-[1.7rem] mb-1">Reviews</h1>
            <p class="text-[#888888ed] text-[.95rem]">Capture your thoughts and experiences with a game.</p>
        </div>
        <form method="POST" action="/reviews/create" class="mt-10">@csrf
            <x-form.input type="textarea" name="review" rows="5" id="review" placeholder="What's your review?" class="placeholder:text-base-content resize-none border-[#88888822]" />
        </form>
    </x-section>
    <x-section class="px-40">
        <nav class="flex flex-row justify-start gap-3">
            <x-badge-link href="/" :active="request()->is('/')">All</x-badge-link>
            <x-badge-link href="/highest-rated" :active="request()->is('highest-rated')">Highest Rated</x-badge-link>
            <x-badge-link href="/lowest-rated" :active="request()->is('lowest-rated')">Lowest Rated</x-badge-link>
            <x-badge-link href="/popular" :active="request()->is('popular')">Popular</x-badge-link>
            <x-badge-link href="/oldest" :active="request()->is('oldest')">Oldest</x-badge-link>
            <x-badge-link href="/hot-reviews" :active="request()->is('hot-reviews')">Hot</x-badge-link>
            <x-badge-link href="/recommended" :active="request()->is('recommended')">Recommended</x-badge-link>
            <x-badge-link href="/not_recommended" :active="request()->is('not_recommended')">Not Recommended</x-badge-link>
            <x-badge-link href="/essential" :active="request()->is('essential')">Essential</x-badge-link>
            <x-badge-link href="/mixed" :active="request()->is('mixed')">Mixed</x-badge-link>
        </nav>
    </x-section>
    <x-section class="px-40 flex flex-col gap-5">
          @foreach($reviews as $review)
                <x-article-link :review="$review"></x-article-link>
        @endforeach
            <div class="mt-5">@if(!empty($reviews->links())){{ $reviews->links() }}@endif</div>
    </x-section>
</x-layout>