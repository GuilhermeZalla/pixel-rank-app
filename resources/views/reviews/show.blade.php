<x-layout>
    <x-slot:title>{{ $review->title }}</x-slot:title>
    <div class="px-4">
        <x-section class="pt-10 pb-5">
            <nav class="flex flex-row justify-between">
                <x-nav-link href="/" class="flex flex-row gap-3 font-bold text-[.90rem] items-center hover:text-accent">
                    <x-heroicon-s-arrow-small-left class="size-5" />Back to Reviews</x-nav-link>
                       @if($review->contains_spoilers)<span class="text-center text-[.95rem] font-bold flex flex-row gap-2 px-4 py-3 mb-7 justify-center items-center bg-[#181818] rounded-2xl"><x-heroicon-s-exclamation-triangle class="text-orange-600 size-5"/>Warning: This review contains spoilers</span>@endif
                <div class="flex flex-row gap-3">
                    @if (!empty($review))
                        @can('update', $review)
                            <x-badge-link class="text-[.80rem] py-4">
                                <x-heroicon-s-arrow-top-right-on-square class="size-4" />Edit
                                Icon</x-badge-link>
                        @endcan
                        @can('delete', $review)
                            <x-badge-link for="my_modal_7" type="label" class="text-[.80rem] py-4"><x-heroicon-s-trash class="size-4" />Delete</x-badge-link>
                        @endcan
                    @endif
                </div>
            </nav>
        </x-section>
        <x-section class="flex flex-col">
            <div class="flex flex-row bg-[#181818] p-5 rounded-2xl">
                 <x-cover class="h-full w-60 object-contain rounded-[20px]"
                src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $review->game_cover }}.jpg" alt="Review Cover"></x-cover>
                <div class="flex flex-col justify-between px-7">
                    <h2 class="flex flex-row items-center text-[.85rem] gap-2"><img @if(!empty($review->user->avatar)) src="{{ asset($review->user->avatar) }}" @else src="https://ui-avatars.com/api/?name={{ urlencode($review->user->nickname) }}"@endif alt="User" class="rounded-[50%] h-8 border-2 border-secondary"> <div><span class="opacity-70">Review by </span><strong class="font-bold">{{ $review->user->nickname }}</strong></div><span class="text-[1rem] opacity-70">&middot;</span> <span class="flex flex-row items-center gap-1.5 opacity-70 text-[.80rem]">
                                    <x-heroicon-o-eye
                                        class="size-3.5" />{{ $review->views }} @if($review->views === 0 || $review->views === 1 ) view @else views @endif
                                </span></h2>
                    <ul class="flex flex-row flex-wrap gap-4 rounded-2xl">
                        <li class="w-[calc(50%-0.5rem)] ">@if($game['platforms_count'] > 1) Plataformas @else Plataforma @endif<br><strong>{{ $game['platforms'] }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">Plataforma Jogada<br><strong>{{ $review->platform_playable }}</strong></li>
                        <li class="w-[calc(50%-0.5rem)] ">Lançamento<br><strong>{{ $game['release_date'] }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">
                            Nota<br><strong>{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</strong></li>
                    </ul>
                    <div class="py-3">
                        <h2 class="font-bold">Summário</h2>
                        <p>{{ $game['summary'] }}</p>
                    </div>
                    <ul class="flex flex-row gap-5 text-[.90rem]">
                        <li>
                            <x-badge-link href="/{{ $review->recommendation }}" type="link"
                                class="rounded-[10px] px-2 py-0">{{ $review->recommendation->label() }}</x-badge-link>
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-o-calendar class="size-4.5" />Published
                            in {{ $review->getPublishedDate() }}
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-s-arrow-path class="size-4.5" />Updated
                            in {{ $review->getUpdatedDate() }}
                        </li>
                        <li><a href="#comments" class="flex flex-row items-center gap-1">
                                <x-heroicon-o-chat-bubble-oval-left class="size-4.5" />{{ count($review->comments) }}
                                comments</a></li>
                    </ul>
                </div>
            </div>
            <div class="py-8 flex flex-col gap-3 mt-5">
                <article class="prose prose-invert max-w-none break-all">
                    <h1 class="text-4xl font-bold mb-8">{{ $review->title }}</h1>
                    {!! nl2br(e($review->body)) !!}
                </article>
                <div class="flex flex-row justify-start gap-5 mt-8">
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-5">
                        <h3 class="font-bold flex flex-row gap-3 items-center">
                            <x-heroicon-o-hand-thumb-up class="size-4.5 text-primary" />Pontos positivos
                        </h3>
                        <ul class="list-disc pl-8">
                            @if(!empty($pros_cons['pros']))
                            @foreach ($pros_cons['pros'] as $pro)
                                <li>{{ $pro->content }}</li>
                            @endforeach
                            @else
                                <li>Nenhum ponto positivo</li>
                            @endif
                        </ul>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-4">
                        <h3 class="font-bold flex flex-row gap-3 items-center">
                            <x-heroicon-o-hand-thumb-down class="size-4.5 text-warning" />Pontos negativos
                        </h3>
                        <ul class="list-disc pl-8">
                            @if(!empty($pros_cons['cons']))
                            @foreach ($pros_cons['cons'] as $cons)
                                <li>{{ $cons->content }}</li>
                            @endforeach
                            @else
                                <li>Nenhum ponto negativo</li>
                            @endif
                        </ul>
                    </div>
                </div>
                <hr class="mt-8 mb-5 border-t border-white/8" />
            </div>
        </x-section>
        <!-- Comments Section -->
        <x-section class="flex flex-col px-3">
            <h2 class="font-bold text-[1.3rem] mb-5">Comentários (<span
                    id="comments-count">{{ count($review->comments) }}</span>)</h2>
            <form id="comment-form" method="POST" action="/comments" class="w-full">
                @csrf
                <x-form.input type="textarea" name="comment" id="comment" placeholder="Participe da discussão"
                    class="placeholder:text-base-content resize-none border-[#88888822] rounded-xl" rows="5" minlength="5" maxlength="2000" />
                <input type="text" name="review" value="{{ $review->id }}" class="hidden" />
            </form>
            @if (!empty($review->comments))
                <ul id="comments" class="flex flex-col gap-3 mt-7">
                    @foreach ($review->comments as $comment)
                     <x-comment :comment="$comment"></x-comment>
                    @endforeach
                </ul>
            @endif
        </x-section>
    </div>
    <!-- Delete Review Confirmation Modal -->
    <input type="checkbox" id="my_modal_7" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box w-95 border border-[#88888833] bg-[#0E0E0E] rounded-[20px] px-6">
            <div class="flex flex-row justify-between items-center">
                <h3 class="text-lg font-bold">Delete Review</h3>
                <label class="btn font-bold bg-transparent shadow-none border-0 outline-0 m-0 p-0" for="my_modal_7">
                    <x-heroicon-o-x-mark class="size-4.5" /></label>
            </div>
            <p class="py-4 text-center text-[1.2rem] my-5 flex flex-col gap-5">Are you sure you want to delete this review? <br><span class="text-[1rem]">(This action can't be undone)</span></p>
            <div class="flex flex-row justify-between gap-5 mb-2">
                <label class="btn font-bold flex-1/2 bg-transparent border border-[#8888884A] hover:bg-black rounded-lg" for="my_modal_7">CANCEL</label>
                <button type="submit" form="delete-review" class="btn font-bold flex-1/2 bg-red-500 rounded-lg">DELETE</button>
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
                    alert('Erro ao enviar comentário');
                    return;
                }

                const data = await response.json();

                let avatar = "https://ui-avatars.com/api/?name=${encodeURIComponent(data.comment.user.name)}"
                if(data.comment.user.avatar){
                     avatar = '/storage/' + data.comment.user.avatar;
                }

                document.getElementById('comments').insertAdjacentHTML('afterbegin', `
    <li id="comment-${data.comment.id}" class="flex flex-row gap-4 border-b border-white/8 pt-8 pb-4">

        <img src="${avatar}"
             alt="User" class="rounded-[50%] h-13">

        <div class="w-full">
            <span class="flex flex-row justify-between mb-3">
                 <h3 class="font-bold text-[.90rem] flex flex-row gap-2 items-center">${data.comment.user.nickname} <span class="text-[1rem] text-[#ffffff97]">&middot;</span><span
                        class="text-[.75rem] text-[#ffffff97] font-normal">${data.comment.posted_date}</span></h3>

                    <ul class="">
                    @if(!empty($comment))
                    @can('delete', $comment)
                        <li id="comment-${data.comment.id}"> <button class="delete-comment text-red-500 cursor-pointer" data-id="${data.comment.id}">Delete</button></li>
                    @endcan
                    @endif
                </ul>
            </span>
            <p class="break-all"> ${data.comment.body}</p>

        </div>
    </li>
    `);
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
                alert('Erro ao deletar comentário');
                return;
            }

            document.getElementById(`comment-${commentId}`).remove();

            document.getElementById('comments-count').textContent = Number(document.getElementById('comments-count').textContent) - 1;
        });
    </script>
</x-layout>