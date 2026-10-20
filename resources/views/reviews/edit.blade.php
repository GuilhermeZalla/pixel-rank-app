<x-layout>
    <x-slot:title>{{ __('reviews.edit_title') }}</x-slot:title>
<main class="container mx-auto max-w-4xl px-4 py-8">
    <h1 class="mb-6 text-3xl font-bold">{{ __('reviews.edit_heading') }}</h1>
    <form method="POST" action="/reviews/{{ $review->id }}" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="game_id" value="{{ $review->game_id }}">
        <input type="hidden" name="game_name" value="{{ $review->game_name }}">
        <input type="hidden" name="game_cover" value="{{ $review->game_cover }}">

        <label class="form-control w-full">
            <span class="label-text mb-2">{{ __('reviews.title') }}</span>
            <input class="input input-bordered w-full" name="title" value="{{ old('title', $review->title) }}" required maxlength="120">
            @error('title')<span class="mt-1 text-error">{{ $message }}</span>@enderror
        </label>

        <fieldset class="form-control">
            <legend class="mb-2">{{ __('reviews.recommendation') }}</legend>
            @foreach (\App\ReviewRecommendation::cases() as $recommendation)
                <label class="label cursor-pointer justify-start gap-3">
                    <input class="radio" type="radio" name="recommendation" value="{{ $recommendation->value }}" @checked(old('recommendation', $review->recommendation->value) === $recommendation->value)>
                    <span class="label-text">{{ __('reviews.recommendations.' . $recommendation->value) }}</span>
                </label>
            @endforeach
            @error('recommendation')<span class="text-error">{{ $message }}</span>@enderror
        </fieldset>

        <label class="form-control w-full">
            <span class="label-text mb-2">{{ __('reviews.rating') }}</span>
            <input class="input input-bordered w-full" type="number" name="rating" min="0" max="10" step="0.5" value="{{ old('rating', $review->rating) }}" required>
            @error('rating')<span class="mt-1 text-error">{{ $message }}</span>@enderror
        </label>

        <label class="form-control w-full">
            <span class="label-text mb-2">{{ __('reviews.review') }}</span>
            <textarea class="textarea textarea-bordered min-h-48 w-full" name="body" required>{{ old('body', $review->body) }}</textarea>
            @error('body')<span class="mt-1 text-error">{{ $message }}</span>@enderror
        </label>

        <label class="form-control w-full">
            <span class="label-text mb-2">{{ __('reviews.platform') }}</span>
            <input class="input input-bordered w-full" name="platform_playable" value="{{ old('platform_playable', $review->platform_playable) }}" required>
            @error('platform_playable')<span class="mt-1 text-error">{{ $message }}</span>@enderror
        </label>

        <input type="hidden" name="contains_spoilers" value="0">
        <label class="label cursor-pointer justify-start gap-3">
            <input class="checkbox" type="checkbox" name="contains_spoilers" value="1" @checked(old('contains_spoilers', $review->contains_spoilers))>
            <span class="label-text">{{ __('reviews.contains_spoilers') }}</span>
        </label>
        <div class="flex flex-wrap gap-3">
            <a class="btn btn-ghost" href="/reviews/{{ $review->id }}">{{ __('reviews.cancel') }}</a>
            <button class="btn btn-primary" type="submit">{{ __('reviews.save_changes') }}</button>
        </div>
    </form>
</main>
</x-layout>
