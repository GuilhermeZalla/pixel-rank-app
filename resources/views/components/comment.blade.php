@props(['dashboard' => false, 'comment' => '', 'review' => ''])

@php
$avatarURL = !empty($comment->user->avatar)
    ? $comment->user->avatar_url
    : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->nickname);
@endphp

@if($dashboard)
    <li id="comment-{{ $comment->id }}"
        class="border border-[#88888833] rounded-[10px] bg-base-300 group hover:border-primary hover:bg-base-200 transition-all duration-300 ease-in-out">
        @if($comment->review)
            <a href="/reviews/{{ $comment->review->id }}" class="flex flex-row items-start gap-3 sm:gap-4 p-4 sm:p-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded-[10px]">
                <div class="avatar shrink-0">
                    <div class="w-10 sm:w-11 rounded-full overflow-hidden ring-1 ring-white/10">
                        <img src="{{ $avatarURL }}" alt="{{ __('reviews.user_avatar_alt') }}" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="w-full min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-[.90rem] flex items-center gap-2">
                            <span>{{ $comment->user->nickname }}</span>
                            <span class="text-xs text-base-content/40">&middot;</span>
                            <span class="text-xs text-base-content/60 font-normal">{{ $comment->getPostedDate() }}</span>
                        </h3>
                    </div>
                    <p class="break-words text-sm text-base-content/85 my-3 leading-relaxed">
                        {{ Str::limit($comment->body, 220) }}
                    </p>
                    <div class="flex items-center gap-1.5 text-xs text-base-content/70">
                        <x-heroicon-o-document-text class="size-4 shrink-0 text-accent/80" />
                        <span>{{ __('dashboard.comment_made_in') }}</span>
                        <strong class="font-bold text-base-content group-hover:text-accent transition-colors truncate max-w-[200px] sm:max-w-md">
                            {{ $comment->review->title }}
                        </strong>
                    </div>
                </div>
            </a>
        @else
            <div class="flex flex-row items-start gap-3 sm:gap-4 p-4 sm:p-5 rounded-[10px]">
                <div class="avatar shrink-0">
                    <div class="w-10 sm:w-11 rounded-full overflow-hidden ring-1 ring-white/10">
                        <img src="{{ $avatarURL }}" alt="{{ __('reviews.user_avatar_alt') }}" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="w-full min-w-0">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="font-bold text-[.90rem] flex items-center gap-2">
                            <span>{{ $comment->user->nickname }}</span>
                            <span class="text-xs text-base-content/40">&middot;</span>
                            <span class="text-xs text-base-content/60 font-normal">{{ $comment->getPostedDate() }}</span>
                        </h3>
                    </div>
                    <p class="break-words text-sm text-base-content/85 my-3 leading-relaxed">
                        {{ Str::limit($comment->body, 220) }}
                    </p>
                    <div class="flex items-center gap-1.5 text-xs text-base-content/50 italic">
                        <x-heroicon-o-document-text class="size-4 shrink-0 opacity-60" />
                        <span>{{ __('dashboard.review_removed') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </li>
@else
    <li id="comment-{{ $comment->id }}" @class(['comment-card group flex min-w-0 gap-3 rounded-2xl border border-base-content/10 bg-base-300 p-4 transition-colors hover:border-primary/40 sm:gap-4 sm:p-5', 'ml-2 border-l-2 border-l-primary/40 sm:ml-5' => $comment->reply_to_id])>
        <img src="{{ $avatarURL }}" alt="" aria-hidden="true" class="size-11 shrink-0 rounded-full object-cover ring-1 ring-base-content/10 sm:size-12">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2">
                <div class="flex min-w-0 flex-wrap items-center gap-x-2 gap-y-1">
                    <h3 class="break-words text-sm font-bold text-base-content">{{ $comment->user->nickname }}</h3>
                    @if($review && (int) $comment->user_id === (int) $review->user_id)
                        <span class="rounded-full bg-primary/15 px-2 py-0.5 text-[.68rem] font-bold text-primary">{{ __('reviews.review_author_badge') }}</span>
                    @endif
                    <time class="text-xs text-base-content/60" datetime="{{ $comment->created_at?->toISOString() }}">{{ $comment->getPostedDate() }}</time>
                </div>
                @auth
                    @if(auth()->user()->hasVerifiedEmail())
                        <div class="flex flex-wrap gap-1">
                        @can('update', $comment)
                            <button type="button" class="edit-comment min-h-11 rounded-lg px-3 text-sm font-semibold text-primary transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" data-id="{{ $comment->id }}">{{ __('reviews.comment_edit') }}</button>
                        @endcan
                        @can('delete', $comment)
                            <button type="button" class="delete-comment min-h-11 min-w-11 rounded-lg px-3 text-sm font-semibold text-error transition-colors hover:bg-error/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" data-id="{{ $comment->id }}" aria-label="{{ __('reviews.comment_delete') }}: {{ $comment->user->nickname }}">
                                {{ __('reviews.comment_delete') }}
                            </button>
                        @endcan
                        </div>
                    @endif
                @endauth
            </div>
            <div class="comment-content">
                @if($comment->reply_to_id && $comment->replyTo?->user)
                    <p class="mt-3 text-xs text-base-content/60">{{ __('reviews.comment_replying_to', ['nickname' => $comment->replyTo->user->nickname]) }}</p>
                @endif
                <p class="comment-body mt-3 whitespace-pre-wrap break-words [overflow-wrap:anywhere] text-sm leading-relaxed text-base-content/90">{{ $comment->body }}</p>
            </div>
            @auth
                @if(auth()->user()->hasVerifiedEmail())
                    @can('update', $comment)
                        <form class="comment-edit-form mt-3 hidden" data-id="{{ $comment->id }}">
                            <label for="comment-edit-{{ $comment->id }}" class="sr-only">{{ __('reviews.comment_edit_label') }}</label>
                            <textarea id="comment-edit-{{ $comment->id }}" rows="4" minlength="5" maxlength="2000" required class="comment-edit-input w-full resize-y rounded-xl border border-base-content/15 bg-base-200 p-3 text-sm text-base-content focus:border-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent">{{ $comment->body }}</textarea>
                            <p class="comment-edit-error mt-2 hidden text-sm text-error" role="alert"></p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="submit" class="comment-edit-save btn btn-primary min-h-11">{{ __('reviews.comment_edit_save') }}</button>
                                <button type="button" class="comment-edit-cancel btn btn-ghost min-h-11">{{ __('reviews.comment_edit_cancel') }}</button>
                            </div>
                        </form>
                    @endcan
                @endif
            @endauth
            @auth
                @if(auth()->user()->hasVerifiedEmail())
                    <div class="comment-interactions mt-3 flex flex-wrap items-center gap-2">
                        <button type="button" class="reply-comment min-h-11 rounded-lg px-3 text-sm font-semibold text-primary transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" data-id="{{ $comment->id }}">{{ __('reviews.comment_reply') }}</button>
                        <button type="button" class="comment-like min-h-11 rounded-lg px-3 text-sm font-semibold transition-colors hover:bg-primary/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent {{ $comment->liked_by_current_user ? 'text-primary' : 'text-base-content/70' }}" data-id="{{ $comment->id }}" aria-pressed="{{ $comment->liked_by_current_user ? 'true' : 'false' }}">
                            <span class="comment-like-label">{{ $comment->liked_by_current_user ? __('reviews.comment_liked') : __('reviews.comment_like') }}</span>
                            <span class="comment-like-count">{{ $comment->likes_count ?? 0 }}</span>
                        </button>
                    </div>
                    <form class="reply-comment-form mt-3 hidden" data-id="{{ $comment->id }}">
                        <label for="comment-reply-{{ $comment->id }}" class="sr-only">{{ __('reviews.comment_reply_label') }}</label>
                        <textarea id="comment-reply-{{ $comment->id }}" rows="3" minlength="5" maxlength="2000" required class="reply-comment-input w-full resize-y rounded-xl border border-base-content/15 bg-base-200 p-3 text-sm text-base-content focus:border-accent focus:outline-none focus-visible:ring-2 focus-visible:ring-accent" placeholder="{{ __('reviews.comment_reply_placeholder') }}"></textarea>
                        <p class="reply-comment-error mt-2 hidden text-sm text-error" role="alert"></p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="submit" class="reply-comment-submit btn btn-primary min-h-11">{{ __('reviews.comment_reply_submit') }}</button>
                            <button type="button" class="reply-comment-cancel btn btn-ghost min-h-11">{{ __('reviews.comment_edit_cancel') }}</button>
                        </div>
                    </form>
                @endif
            @endauth
        </div>
    </li>
@endif
