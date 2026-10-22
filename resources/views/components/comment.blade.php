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
    <li id="comment-{{ $comment->id }}" class="flex flex-row gap-4 border-b border-white/8 pt-8 pb-4 hover:bg-base-200/50 transition-colors duration-300 rounded-lg px-4 -mx-4">
            <x-cover src="{{ $avatarURL }}" alt="{{ __('reviews.user_avatar_alt') }}"
            class="rounded-[50%] h-13 shadow-md"></x-cover>
        <div class="w-full">
            <span class="flex flex-row justify-between mb-3">
                <h3 class="font-bold text-[.90rem] flex flex-row gap-2 items-center">{{ $comment->user->nickname }}<span class="text-[1rem] text-[#ffffff97]">&middot;</span> <span
                        class="text-[.75rem] text-[#ffffff97] font-normal">{{ $comment->getPostedDate() }}</span></h3>
                <ul>
                    @can('delete', $comment)
                        <li id="comment-{{ $comment->id }}"> <button class="delete-comment text-red-500 cursor-pointer hover:text-red-400 transition-colors" data-id="{{ $comment->id }}">{{ __('reviews.comment_delete') }}</button></li>
                    @endcan
                </ul>
            </span>
            <p class="break-words leading-relaxed text-base-content/90"> {!! nl2br(e($comment->body)) !!}</p>
        </div>
    </li>
@endif
