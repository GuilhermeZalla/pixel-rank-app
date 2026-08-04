@props(['dashboard' => false, 'comment' => ''])

@if($dashboard)
  <li id="comment-{{ $comment->id }}" class="flex flex-row gap-4 border border-[#88888833] py-5 px-3.5 rounded-[10px] cursor-pointer hover:border-primary transition-all duration-300 ease-in-out">
        <x-cover src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->nickname) }}" alt="User Profile Image" class="rounded-[50%] h-10"></x-cover>
        <div class="w-full">
            <span class="flex flex-row justify-between">
                 <h3 class="font-bold text-[.90rem] flex flex-col">{{ $comment->user->nickname }} <span class="text-[.75rem] text-[#ffffff97] font-normal">{{ $comment->getPostedDate() }}</span></h3>
                <div class="flex flex-row">
                    <div class="navbar bg-base-100 shadow-sm p-0 self-center">
                        <div class="navbar-center hidden lg:flex">
                            <ul class="menu menu-horizontal px-1">
                                <li>
                                    <details>
                                        <summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                            </svg>
                                        </summary>
                                        <ul class="p-2 bg-base-100 w-40 z-1">
                                            @can('delete', $comment)
                                                <li id="comment-{{ $comment->id }}"> <button class="delete-comment text-red-500"
                                                        data-id="{{ $comment->id }}"> Delete
                                                    </button></li>
                                            @endcan
                                        </ul>
                                    </details>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </span>
            <p class="break-all">{{ Str::limit($comment->body, 150) }}</p>
        </div>
    </li>
@else
    <li id="comment-{{ $comment->id }}" class="flex flex-row gap-4 border-b border-white/8 pt-8 pb-4">
        <x-cover src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->nickname) }}" alt="User Profile Image"
            class="rounded-[50%] h-12"></x-cover>
        <div class="w-full">
            <span class="flex flex-row justify-between mb-5">
                <h3 class="font-bold text-[.90rem] flex flex-col">{{ $comment->user->nickname }} <span class="text-[.75rem] text-[#ffffff97] font-normal">{{ $comment->getPostedDate() }}</span></h3>
                <div class="flex flex-row h-10">
                    <div class="navbar bg-base-100 shadow-sm p-0 self-center">
                        <div class="navbar-center hidden lg:flex">
                            <ul class="menu menu-horizontal px-1">
                                <li>
                                    <details>
                                        <summary><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                            </svg>
                                        </summary>
                                        <ul class="p-2 bg-base-100 w-40 z-1">
                                            @can('delete', $comment)
                                                <li id="comment-{{ $comment->id }}"> <button class="delete-comment text-red-500"
                                                        data-id="{{ $comment->id }}"> Delete
                                                    </button></li>
                                            @endcan
                                        </ul>
                                    </details>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </span>
            <p class="break-all"> {!! nl2br(e($comment->body)) !!}</p>
        </div>
    </li>
@endif