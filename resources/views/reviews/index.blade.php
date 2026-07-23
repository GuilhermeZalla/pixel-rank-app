<x-layout>
    <x-slot:title>PixelRank</x-slot:title>
    <section class="flex flex-col">
        <div class="mt-10">
            <h1 class="font-bold text-[1.7rem] mb-1">Reviews</h1>
            <p class="text-[#888888b1] text-[.95rem]">Capture your thoughts and experiences with a game.</p>
        </div>
        <form method="POST" action="/reviews/create" class="mt-10">@csrf
            <x-form.input type="textarea" name="review" rows="5" id="review" placeholder="What's your review?" class="placeholder:text-base-content resize-none border-[#88888822]" />
        </form>
    </section>
    <section>
        <nav class="flex flex-row justify-start gap-3">
            <x-badge-link href="/" count="2" :active="request()->is('/')">Highest Rated</x-badge-link>
            <x-badge-link href="/popular" count="2" :active="request()->is('popular')">Popular</x-badge-link>
            <x-badge-link href="/latest" count="2" :active="request()->is('latest')">Latest</x-badge-link>
            <x-badge-link href="/oldest" count="2" :active="request()->is('oldest')">Oldest</x-badge-link>
        </nav>
    </section>
    <section class="flex flex-wrap gap-5">
        @foreach($reviews as $review)
                <x-article-link :review="$review"></x-article-link>
        @endforeach
    </section>
    <div>

    </div>
</x-layout>