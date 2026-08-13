@props(['dashboard' => false, 'comment' => '', 'review' => ''])

@php
    $avatarURL = !empty($comment->user->avatar) ? asset($comment->user->avatar) : "https://ui-avatars.com/api/?name={{ urlencode($comment->user->nickname) }}";
@endphp

@if($dashboard)
    <li id="comment-{{ $comment->id }}"
        class="border-2 border-[#88888833] py-5 px-3.5 rounded-[10px] cursor-pointer group hover:border-primary hover:bg-[#141414] transition-all duration-300 ease-in-out">
        <a href="/reviews/{{ $comment->review->id }}" class="flex flex-row gap-4">
            <x-cover src="{{ $avatarURL }}"
                alt="User Profile Image" class="rounded-[50%] h-10"></x-cover>
            <div class="w-full">
                <span class="flex flex-row justify-between">
                    <h3 class="font-bold text-[.90rem] flex flex-row gap-3 items-center">{{ $comment->user->nickname }}
                        <span class="text-[.75rem] text-[#ffffff97] font-normal">{{ $comment->getPostedDate() }}</span></h3>
                    @can('delete', $comment)
                        <x-badge-link form="delete-comment" type="button" class="text-red-500 border-none bg-transparent"><x-heroicon-s-trash
                                class="size-4" /></x-badge-link>
                        <x-form.form-delete id="delete-comment" method="POST" action="/comments/{{ $comment->id }}">@csrf
                            @method('DELETE')
                            <input class="hidden" type="text" name="profile"></x-form.form-delete>
                    @endcan
                </span>
                <p class="break-all mt-3 mb-5">{{ Str::limit($comment->body, 150) }}</p>
                <p class="text-[.75rem] text-[#ffffff97] font-normal">Comment made in <strong class="font-bold group-hover:text-accent">{{ $comment->review->title }}</strong></p>
            </div>
        </a>
    </li>
@else
    <li id="comment-{{ $comment->id }}" class="flex flex-row gap-4 border-b border-white/8 pt-8 pb-4">
        <x-cover src="{{ $avatarURL }}" alt="User Profile Image"
            class="rounded-[50%] h-12"></x-cover>
        <div class="w-full">
            <span class="flex flex-row justify-between mb-5">
                <h3 class="font-bold text-[.90rem] flex flex-col">{{ $comment->user->nickname }} <span
                        class="text-[.75rem] text-[#ffffff97] font-normal">{{ $comment->getPostedDate() }}</span></h3>
                <ul>
                    @can('delete', $comment)
                        <li id="comment-{{ $comment->id }}"> <button class="delete-comment text-red-500 cursor-pointer" data-id="{{ $comment->id }}">Delete</button></li>
                    @endcan
                </ul>
            </span>
            <p class="break-all"> {!! nl2br(e($comment->body)) !!}</p>
        </div>
    </li>
@endif