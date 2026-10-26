<x-layout>
    <x-slot:title>{{ $review->title }}</x-slot:title>
    <div class="px-4 max-w-7xl mx-auto">
        <x-section class="pt-10 pb-5">
            <nav class="flex flex-row justify-between">
                <x-nav-link href="/" class="flex flex-row gap-3 font-bold text-[.90rem] items-center hover:text-accent">
                    <x-heroicon-s-arrow-small-left class="size-5" />{{ __('reviews.back_to_reviews') }}</x-nav-link>
                       @if($review->contains_spoilers)<span class="text-center text-[.95rem] font-bold flex flex-row gap-2 px-4 py-3 mb-7 justify-center items-center bg-base-300 rounded-2xl"><x-heroicon-s-exclamation-triangle class="text-orange-600 size-5"/>{{ __('reviews.spoiler_warning') }}</span>@endif
                <div class="flex flex-row gap-3">
                    @if (!empty($review))
                        @can('update', $review)
                            <x-badge-link href="/reviews/{{ $review->id }}/edit" type="link" class="text-[.80rem] py-4">
                                <x-heroicon-s-arrow-top-right-on-square class="size-4" />{{ __('reviews.edit') }}</x-badge-link>
                        @endcan
                        @can('delete', $review)
                            <x-badge-link for="my_modal_7" type="label" class="text-[.80rem] py-4"><x-heroicon-s-trash class="size-4" />{{ __('reviews.delete') }}</x-badge-link>
                        @endcan
                    @endif
                </div>
            </nav>
        </x-section>
        <x-section class="flex flex-col">
            <div class="flex flex-col gap-5 rounded-2xl bg-base-300 p-6 sm:flex-row sm:p-8 shadow-lg shadow-black/20">
                 <x-cover class="h-full w-full max-w-60 object-contain rounded-[20px] sm:w-60 shadow-lg"
                src="@if(!empty($gameCover))https://images.igdb.com/igdb/image/upload/t_1080p/{{ $gameCover }}.jpg@else{{ asset('images/game-placeholder.svg') }}@endif" alt="{{ __('reviews.review_cover_alt') }}"></x-cover>
                <div class="flex flex-col justify-between px-6 sm:px-8 flex-1">
                    <h2 class="flex flex-row items-center text-[.85rem] gap-2"><img @if(!empty($review->user->avatar_url)) src="{{ $review->user->avatar_url }}" @else src="https://ui-avatars.com/api/?name={{ urlencode($review->user->nickname) }}"@endif alt="{{ __('reviews.user_avatar_alt') }}" class="rounded-[50%] h-8 border-2 border-secondary"> <div><span class="opacity-70">{{ __('reviews.review_by') }} </span><strong class="font-bold">{{ $review->user->nickname }}</strong></div><span class="text-[1rem] opacity-70">&middot;</span> <span class="flex flex-row items-center gap-1.5 opacity-70 text-[.80rem]">
                                    <x-heroicon-o-eye
                                        class="size-3.5" />{{ trans_choice('reviews.views_count', $review->views, ['count' => $review->views]) }}
                                </span></h2>
                    <ul class="flex flex-row flex-wrap gap-4 rounded-2xl">
                        <li class="w-[calc(50%-0.5rem)] ">@if($game['platforms_count'] > 1) {{ __('reviews.platforms') }} @else {{ __('reviews.platforms_single') }} @endif<br><strong>{{ $game['platforms'] ?: __('reviews.not_available') }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">{{ __('reviews.platform_played') }}<br><strong>{{ $review->platform_playable ?: __('reviews.not_available') }}</strong></li>
                        <li class="w-[calc(50%-0.5rem)] ">{{ __('reviews.release') }}<br><strong>{{ $game['release_date'] ?: __('reviews.not_available') }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">
                            {{ __('reviews.rating') }}<br><strong>{{ \App\Support\LocaleFormatter::number((float) $review->rating, 1) }}/10</strong></li>
                    </ul>
                    <div class="py-3">
                        <h2 class="font-bold">{{ __('reviews.summary') }}</h2>
                        <p class="text-base-content/80 leading-relaxed">{{ $game['summary'] ?: __('reviews.not_available') }}</p>
                    </div>
                    <ul class="flex flex-row flex-wrap gap-5 text-[.90rem]">
                        <li>
                            <x-badge-link href="/{{ $review->recommendation }}" type="link"
                                class="rounded-[10px] px-2 py-0">{{ __('reviews.recommendations.' . $review->recommendation->value) }}</x-badge-link>
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-o-calendar class="size-4.5" />{{ __('reviews.published_in', ['date' => $review->getPublishedDate()]) }}
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-s-arrow-path class="size-4.5" />{{ __('reviews.updated_in', ['date' => $review->getUpdatedDate()]) }}
                        </li>
                        <li><a href="#comments" class="flex flex-row items-center gap-1">
                                <x-heroicon-o-chat-bubble-oval-left class="size-4.5" /><span id="review-comments-count"
                                    data-zero="{{ __('reviews.comments_count_zero') }}"
                                    data-one="{{ __('reviews.comments_count_one') }}"
                                    data-many="{{ __('reviews.comments_count_many') }}">{{ trans_choice('reviews.comments_count', $commentCount, ['count' => $commentCount]) }}</span></a></li>
                    </ul>
                </div>
            </div>
            <div class="py-8 flex flex-col gap-3 mt-5">
                <article class="prose prose-invert max-w-none break-all">
                    <h1 class="text-3xl sm:text-4xl font-bold mb-8 text-gradient">{{ $review->title }}</h1>
                    <div class="leading-relaxed text-base-content/90">
                        {!! nl2br(e($review->body)) !!}
                    </div>
                </article>
                <div class="mt-8 flex flex-col justify-start gap-5 md:flex-row">
                    <div class="flex flex-col flex-wrap gap-4 bg-base-300 rounded-2xl p-6 shadow-md flex-1">
                        <h3 class="font-bold flex flex-row gap-3 items-center text-lg">
                            <x-heroicon-o-hand-thumb-up class="size-5 text-primary" />{{ __('reviews.positive') }}
                        </h3>
                        <ul class="list-disc pl-8 space-y-2">
                            @if(!empty($pros_cons['pros']))
                            @foreach ($pros_cons['pros'] as $pro)
                                <li class="text-base-content/80">{{ $pro->content }}</li>
                            @endforeach
                            @else
                                <li class="text-base-content/50">{{ __('reviews.no_positive') }}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-base-300 rounded-2xl p-6 shadow-md flex-1">
                        <h3 class="font-bold flex flex-row gap-3 items-center text-lg">
                            <x-heroicon-o-hand-thumb-down class="size-5 text-warning" />{{ __('reviews.negative') }}
                        </h3>
                        <ul class="list-disc pl-8 space-y-2">
                            @if(!empty($pros_cons['cons']))
                            @foreach ($pros_cons['cons'] as $cons)
                                <li class="text-base-content/80">{{ $cons->content }}</li>
                            @endforeach
                            @else
                                <li class="text-base-content/50">{{ __('reviews.no_negative') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
                <hr class="mt-8 mb-5 border-t border-white/8" />
            </div>
        </x-section>
        <!-- Comments Section -->
        <x-section id="comments-panel" class="flex flex-col px-3"
            data-review-author-id="{{ $review->user_id }}"
            data-review-id="{{ $review->id }}"
            data-current-user-id="{{ auth()->id() ?? '' }}"
            data-can-manage="{{ auth()->check() && auth()->user()->hasVerifiedEmail() ? 'true' : 'false' }}"
            data-comments-url="{{ route('reviews.comments', $review) }}"
            data-author-label="{{ __('reviews.review_author_badge') }}"
            data-edit-label="{{ __('reviews.comment_edit') }}"
            data-edit-field-label="{{ __('reviews.comment_edit_label') }}"
            data-edit-save="{{ __('reviews.comment_edit_save') }}"
            data-edit-cancel="{{ __('reviews.comment_edit_cancel') }}"
            data-edit-saving="{{ __('reviews.comment_edit_saving') }}"
            data-edit-saved="{{ __('reviews.comment_edit_saved') }}"
            data-edit-error="{{ __('reviews.comment_edit_error') }}"
            data-error-short="{{ __('reviews.comment_min_error') }}"
            data-error-long="{{ __('reviews.comment_max_error') }}"
            data-delete-label="{{ __('reviews.comment_delete') }}"
            data-reply-label="{{ __('reviews.comment_reply') }}"
            data-replying-to-label="{{ __('reviews.comment_replying_to', ['nickname' => ':nickname']) }}"
            data-reply-placeholder="{{ __('reviews.comment_reply_placeholder') }}"
            data-reply-submit="{{ __('reviews.comment_reply_submit') }}"
            data-reply-sending="{{ __('reviews.comment_reply_sending') }}"
            data-reply-error="{{ __('reviews.comment_reply_error') }}"
            data-comment-posted="{{ __('reviews.comment_posted_status') }}"
            data-like-label="{{ __('reviews.comment_like') }}"
            data-liked-label="{{ __('reviews.comment_liked') }}"
            data-like-error="{{ __('reviews.comment_like_error') }}"
            data-error-short="{{ __('reviews.comment_min_error') }}"
            data-error-long="{{ __('reviews.comment_max_error') }}"
            data-sort-error="{{ __('reviews.comments_load_error') }}">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 id="comments-heading" class="font-bold text-[1.3rem]">{{ __('reviews.comments_heading') }} (<span id="comments-count" class="text-primary">{{ $commentCount }}</span>)</h2>
                <label class="flex items-center gap-2 text-sm text-base-content/75">
                    <span>{{ __('reviews.comments_sort_label') }}</span>
                    <select id="comments-sort" class="select select-sm min-h-11 bg-base-300">
                        <option value="newest">{{ __('reviews.comments_sort_newest') }}</option>
                        <option value="oldest">{{ __('reviews.comments_sort_oldest') }}</option>
                    </select>
                </label>
            </div>

            @guest
                <div class="rounded-xl border border-base-content/10 bg-base-300 p-4 text-sm text-base-content/80" role="status">
                    {{ __('reviews.comment_login_prompt') }}
                    <a href="{{ route('login') }}" class="ml-1 inline-flex min-h-11 items-center font-bold text-primary underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">{{ __('reviews.comment_login_link') }}</a>
                </div>
            @else
                @if(auth()->user()->hasVerifiedEmail())
                    <form id="comment-form" method="POST" action="/comments" class="w-full" aria-describedby="comment-help"
                        novalidate
                        data-review-author-id="{{ $review->user_id }}"
                        data-error-short="{{ __('reviews.comment_min_error') }}"
                        data-error-long="{{ __('reviews.comment_max_error') }}"
                        data-error-generic="{{ __('reviews.comment_submit_error') }}"
                        data-error-session="{{ __('reviews.comment_session_error') }}"
                        data-success="{{ __('reviews.comment_posted_status') }}"
                        data-sending="{{ __('reviews.comment_sending') }}"
                        data-submit-label="{{ __('reviews.comment_submit') }}"
                        data-author-label="{{ __('reviews.review_author_badge') }}"
                        data-delete-label="{{ __('reviews.comment_delete') }}">
                        @csrf
                        <input type="hidden" name="review" value="{{ $review->id }}">
                        <label for="comment" class="mb-2 block text-sm font-bold">{{ __('reviews.comment_label') }}</label>
                        <textarea name="comment" id="comment" rows="4" minlength="5" maxlength="2000" required
                            aria-describedby="comment-help comment-counter comment-error"
                            class="w-full resize-y rounded-xl border border-base-content/15 bg-base-300 p-4 text-sm text-base-content shadow-sm placeholder:text-base-content/45 focus:border-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent"
                            placeholder="{{ __('reviews.comment_placeholder') }}"></textarea>
                        <div class="mt-2 flex flex-col justify-between gap-1 text-xs text-base-content/65 sm:flex-row sm:items-center">
                            <p id="comment-help">{{ __('reviews.comment_limits') }}</p>
                            <p id="comment-counter" aria-live="off"><span id="comment-character-count">0</span>/2000</p>
                        </div>
                        <p id="comment-error" class="mt-2 hidden text-sm font-medium text-error" role="alert"></p>
                        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-base-content/60">{{ __('reviews.comment_keyboard_hint') }}</p>
                            <button id="comment-submit" type="submit" class="btn btn-primary min-h-11 w-full font-bold sm:w-auto">
                                <span id="comment-submit-spinner" class="loading loading-spinner loading-xs hidden" aria-hidden="true"></span>
                                <span id="comment-submit-label">{{ __('reviews.comment_submit') }}</span>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="rounded-xl border border-warning/30 bg-base-300 p-4 text-sm text-base-content/80" role="status">
                        {{ __('reviews.comment_verify_prompt') }}
                        <a href="{{ route('verification.notice') }}" class="ml-1 inline-flex min-h-11 items-center font-bold text-primary underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">{{ __('reviews.comment_verify_link') }}</a>
                    </div>
                @endif
            @endguest

            <p id="comment-feedback" class="mt-3 hidden rounded-lg px-3 py-2 text-sm" role="status" aria-live="polite"></p>
            <p id="comments-empty" @class(['mt-7 rounded-xl border border-dashed border-base-content/15 px-4 py-6 text-center text-sm text-base-content/65', 'hidden' => $commentCount > 0])>{{ __('reviews.comments_empty') }}</p>
            <ul id="comments" aria-labelledby="comments-heading" class="mt-5 flex flex-col gap-3">
                @foreach ($comments as $comment)
                    <x-comment :comment="$comment" :review="$review"></x-comment>
                @endforeach
            </ul>
            <p id="comments-feedback" class="mt-3 hidden text-sm text-error" role="alert"></p>
            <button id="comments-load-more" type="button" @class(['btn btn-ghost mt-5 min-h-11 self-center', 'hidden' => !$commentsHasMore]) data-offset="{{ $comments->count() }}" data-has-more="{{ $commentsHasMore ? 'true' : 'false' }}" data-loading="{{ __('reviews.comments_loading') }}" data-label="{{ __('reviews.comments_load_more') }}">
                {{ __('reviews.comments_load_more') }}
            </button>
        </x-section>
        @auth
            @if(auth()->user()->hasVerifiedEmail())
                <dialog id="comment-delete-dialog" class="modal" aria-labelledby="comment-delete-title" aria-describedby="comment-delete-description"
                    data-error="{{ __('reviews.comment_delete_error') }}"
                    data-session-error="{{ __('reviews.comment_session_error') }}"
                    data-deleting="{{ __('reviews.comment_deleting') }}"
                    data-delete-label="{{ __('reviews.confirm_delete') }}"
                    data-success="{{ __('reviews.comment_deleted_status') }}">
                    <div class="modal-box max-w-md rounded-2xl bg-base-300 p-6">
                        <h3 id="comment-delete-title" class="text-lg font-bold">{{ __('reviews.comment_delete_confirmation_title') }}</h3>
                        <p id="comment-delete-description" class="mt-3 text-sm text-base-content/75">{{ __('reviews.comment_delete_confirmation') }}</p>
                        <p id="comment-delete-error" class="mt-3 hidden text-sm font-semibold text-error" role="alert"></p>
                        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button id="comment-delete-cancel" type="button" class="btn btn-ghost min-h-11">{{ __('reviews.confirm_cancel') }}</button>
                            <button id="comment-delete-confirm" type="button" class="btn btn-error min-h-11 text-white">
                                <span id="comment-delete-spinner" class="loading loading-spinner loading-xs hidden" aria-hidden="true"></span>
                                <span id="comment-delete-label">{{ __('reviews.confirm_delete') }}</span>
                            </button>
                        </div>
                    </div>
                </dialog>
            @endif
        @endauth
    </div>
    <!-- Delete Review Confirmation Modal -->
    <input type="checkbox" id="my_modal_7" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box w-95 bg-base-300 rounded-[20px] px-8 py-6 shadow-2xl">
            <div class="flex flex-row justify-between items-center mb-6">
                <h3 class="text-lg font-bold">{{ __('reviews.delete_review') }}</h3>
                <label class="btn btn-circle btn-ghost btn-sm" for="my_modal_7">
                    <x-heroicon-o-x-mark class="size-5" /></label>
            </div>
            <p class="py-4 text-center text-[1.2rem] my-5 flex flex-col gap-3">{{ __('reviews.delete_review_confirmation') }} <span class="text-[1rem] text-base-content/70">({{ __('reviews.cannot_undo') }})</span></p>
            <div class="flex flex-row justify-between gap-4 mt-6">
                <label class="btn font-bold flex-1 bg-transparent border border-[#8888884A] hover:bg-base-200 rounded-lg" for="my_modal_7">{{ __('reviews.confirm_cancel') }}</label>
                <button type="submit" form="delete-review" class="btn font-bold flex-1 bg-red-500 hover:bg-red-600 rounded-lg shadow-lg shadow-red-500/30">{{ __('reviews.confirm_delete') }}</button>
            </div>
        </div>
    </div>
    <!-- Delete Review Form -->
    <x-form.form-delete method="POST" action="/reviews/{{ $review->id }}" id="delete-review"></x-form.form-delete>
</x-layout>
