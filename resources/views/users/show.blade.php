<x-layout>
    <x-slot:title>{{ __('dashboard.profile_title', ['nickname' => $user->nickname]) }}</x-slot:title>
<main class="container mx-auto max-w-5xl px-4 py-8">
    <section class="card card-side flex-col items-center gap-5 bg-base-200 p-6 sm:flex-row">
        <div class="avatar">
            <div class="w-24 rounded-full">
                @if ($user->avatar)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}" alt="{{ __('dashboard.avatar_alt') }}">
                @else
                    <div class="flex aspect-square items-center justify-center bg-base-300 text-3xl font-bold" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->nickname, 0, 1)) }}</div>
                @endif
            </div>
        </div>
        <div>
            <h1 class="text-3xl font-bold">{{ $user->nickname }}</h1>
            @if ($user->bio)<p class="mt-2">{{ $user->bio }}</p>@endif
        </div>
    </section>

    <h2 class="mb-4 mt-8 text-2xl font-semibold">{{ __('dashboard.public_reviews') }}</h2>
    <div class="space-y-4">
        @forelse ($reviews as $review)
            <article class="card bg-base-200 p-5">
                <a class="link link-hover text-xl font-semibold" href="/reviews/{{ $review->id }}">{{ $review->title }}</a>
                <p class="mt-2">{{ \Illuminate\Support\Str::limit($review->body, 240) }}</p>
                <span class="mt-3 text-sm opacity-70">{{ $review->game_name }} · {{ $review->rating }}/10</span>
            </article>
        @empty
            <p class="opacity-70">{{ __('dashboard.no_public_reviews') }}</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $reviews->links() }}</div>
</main>
</x-layout>
