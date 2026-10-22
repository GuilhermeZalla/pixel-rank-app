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
                                <x-heroicon-o-chat-bubble-oval-left class="size-4.5" />{{ trans_choice('reviews.comments_count', count($review->comments), ['count' => count($review->comments)]) }}</a></li>
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
        <x-section class="flex flex-col px-3">
            <h2 class="font-bold text-[1.3rem] mb-5">{{ __('reviews.comments_heading') }} (<span
                    id="comments-count" class="text-primary">{{ count($review->comments) }}</span>)</h2>
            <form id="comment-form" method="POST" action="/comments" class="w-full">
                @csrf
                <x-form.input type="textarea" name="comment" id="comment" :placeholder="__('reviews.comment_placeholder')"
                    class="placeholder:text-base-content resize-none border-[#88888822] rounded-xl shadow-lg shadow-black/10" rows="5" minlength="5" maxlength="2000" />
                <input type="text" name="review" value="{{ $review->id }}" class="hidden" />
            </form>
            <ul id="comments" class="flex flex-col gap-3 mt-7">
                @foreach ($review->comments as $comment)
                 <x-comment :comment="$comment"></x-comment>
                @endforeach
            </ul>
        </x-section>
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
    <script>
        const form = document.getElementById('comment-form');
        const textarea = document.getElementById('comment');

        if (form && textarea) {

            textarea.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    form.requestSubmit();
                }
            });

            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const response = await fetch('/comments', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                });

                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                if (!response.ok) {
                    alert(@js(__('reviews.comment_submit_error')));
                    return;
                }

                const data = await response.json();

                const commentItem = document.createElement('li');
                commentItem.id = `comment-${data.comment.id}`;
                commentItem.className = 'flex flex-row gap-4 border-b border-white/8 pt-8 pb-4';

                const avatar = document.createElement('img');
                avatar.src = data.comment.user.avatar_url
                    ? data.comment.user.avatar_url
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(data.comment.user.nickname)}`;
                avatar.alt = @js(__('reviews.user_avatar_alt'));
                avatar.className = 'rounded-[50%] h-13';
                commentItem.append(avatar);

                const content = document.createElement('div');
                content.className = 'w-full';

                const header = document.createElement('span');
                header.className = 'flex flex-row justify-between mb-3';

                const author = document.createElement('h3');
                author.className = 'font-bold text-[.90rem] flex flex-row gap-2 items-center';
                author.append(document.createTextNode(data.comment.user.nickname));

                const separator = document.createElement('span');
                separator.className = 'text-[1rem] text-[#ffffff97]';
                separator.textContent = '·';
                author.append(separator);

                const postedDate = document.createElement('span');
                postedDate.className = 'text-[.75rem] text-[#ffffff97] font-normal';
                postedDate.textContent = data.comment.posted_date;
                author.append(postedDate);
                header.append(author);

                const actions = document.createElement('ul');
                const action = document.createElement('li');
                const deleteButton = document.createElement('button');
                deleteButton.className = 'delete-comment text-red-500 cursor-pointer';
                deleteButton.dataset.id = data.comment.id;
                deleteButton.textContent = @js(__('reviews.comment_delete'));
                action.append(deleteButton);
                actions.append(action);
                header.append(actions);
                content.append(header);

                const body = document.createElement('p');
                body.className = 'break-all';
                body.textContent = data.comment.body;
                content.append(body);
                commentItem.append(content);

                document.getElementById('comments').prepend(commentItem);
                textarea.value = '';

                document.getElementById('comments-count').textContent = Number(document.getElementById('comments-count').textContent) + 1;
            });
        }

        document.getElementById('comments').addEventListener('click', async function (event) {

            if (!event.target.classList.contains('delete-comment')) {
                return;
            }

            const button = event.target;
            const commentId = button.dataset.id;

            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                alert(@js(__('reviews.comment_delete_error')));
                return;
            }

            document.getElementById(`comment-${commentId}`).remove();

            document.getElementById('comments-count').textContent = Number(document.getElementById('comments-count').textContent) - 1;
        });
    </script>
</x-layout>
