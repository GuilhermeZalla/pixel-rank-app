<x-layout>
    <x-slot:title>{{ $review->title }}</x-slot:title>
    <div class="px-4 sm:px-0 md:px-40 lg:px-50">
        <x-section class="py-10">
            <nav class="flex flex-row justify-between">
                <x-nav-link href="/" class="flex flex-row gap-3 font-bold text-[.90rem] items-center hover:text-accent">
                    <x-heroicon-s-arrow-small-left class="size-5" />Back to Reviews</x-nav-link>
                <div class="flex flex-row gap-3">
                    @if (!empty($review))
                        @can('update', $review)
                            <x-badge-link>
                                <x-heroicon-s-arrow-top-right-on-square class="size-3.5" />Edit
                                Icon</x-badge-link>
                        @endcan
                        @can('delete', $review)
                            <x-badge-link for="my_modal_7" type="label"><x-heroicon-s-trash class="size-3.5" />Delete</x-badge-link>
                        @endcan
                    @endif
                </div>
            </nav>
        </x-section>
        <x-section class="flex flex-col">
            @if($review->contains_spoilers)<span class="text-center text-[.95rem] font-bold flex flex-row gap-2 p-3 mb-7 justify-center items-center bg-[#181818] rounded-2xl"><x-heroicon-s-exclamation-triangle class="text-orange-600 size-4.5"/>Warning: This review contains spoilers</span>@endif
            <div class="aspect-16/6 w-full overflow-hidden rounded-tr-xl rounded-tl-xl relative">
                @if(!empty($game['cover']['id']) && in_array($game['cover']['id'], $game['cover']))
                    <img src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $game['cover']['image_id'] }}.jpg" alt="Game cover"
                        class="w-full h-full object-cover object-top rounded-tr-xl rounded-tl-xl">
                @else
                    <img src="https://images.igdb.com/igdb/image/upload/t_1080p/{{ $game['cover'] }}.jpg" alt="Game cover"
                        class="w-full h-full object-cover object-top rounded-tr-xl rounded-tl-xl">
                @endif
                <div class="absolute inset-0 rounded-tr-xl rounded-tl-xl bg-linear-to-t from-black via-black/20 via-100% to-transparent"></div>
            </div>
            <div class="py-8 px-3 flex flex-col gap-3 -mt-30 relative z-10">
                <h1 class="text-4xl font-bold mb-2">{{ $review->title }}</h1>
                <div class="flex flex-col gap-6">
                    <ul class="flex flex-row gap-5">
                        <li>
                            <x-badge-link href="/{{ $review->recommendation }}" type="link" class="text-[.90rem] rounded-[10px] px-2 py-0">{{ $review->recommendation->label() }}</x-badge-link>
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-o-calendar class="size-4.5" />Published
                            in {{ $review->getPublishedDate() }}
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-s-arrow-path class="size-4.5" />Updated
                            in {{ $review->getUpdatedDate() }}
                        </li>
                        <li class="flex flex-row items-center gap-1">
                            <x-heroicon-o-user class="size-4.5" />By
                            {{ $review->user->nickname }}
                        </li>
                        <li><a href="#comments" class="flex flex-row items-center gap-1">
                                <x-heroicon-o-chat-bubble-oval-left class="size-4.5" />{{ count($review->comments) }}
                                comments</a></li>
                    </ul>
                    <ul class="flex flex-row flex-wrap gap-4 bg-[#181818] rounded-2xl p-4">
                        <li class="w-[calc(50%-0.5rem)] ">Plataforma(s)<br><strong>{{ $game['platforms'] }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">Plataforma Jogada<br><strong>{{ $review->platform_playable }}</strong></li>
                        <li class="w-[calc(50%-0.5rem)] ">Lançamento<br><strong>{{ $game['release_date'] }}</strong>
                        </li>
                        <li class="w-[calc(50%-0.5rem)] ">
                            Nota<br><strong>{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</strong></li>
                    </ul>
                    <h2 class="font-bold">Resumo</h2>
                    <p>{{ $game['summary'] }}</p>
                </div>
                <hr class="my-8 border-t border-white/8" />
                <article class="prose prose-invert max-w-none break-all">
                    <h2 class="font-bold mb-5">Review</h2>
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

                document.getElementById('comments').insertAdjacentHTML('afterbegin', `
    <li id="comment-${data.comment.id}" class="flex flex-row gap-4 border-b border-white/8 py-8">

        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(data.comment.user.name)}"
             alt="User" class="rounded-[50%] h-12">

        <div class="w-full">
            <span class="flex flex-row justify-between mb-3">
                <h3 class="font-bold">${data.comment.user.name}</h3>
                <h4 class="label text-[.90rem]">${data.comment.posted_date}</h4>
                <div class="navbar bg-base-100 shadow-sm">
                    <div class="navbar-center hidden lg:flex">
                        <ul class="menu menu-horizontal px-1">
                            <li>
                                <details>
                                    <summary><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                    </svg>
                                    </summary>
                                    <ul class="p-2 bg-base-100 w-40 z-1">
                                        <li>
                                            <button
                                                class="delete-comment text-red-500"
                                                data-id="${data.comment.id}">
                                                Delete
                                            </button>
                                        </li>
                                    </ul>
                                </details>
                            </li>
                        </ul>
                    </div>
                </div>
            </span>

            <p class="break-all">${data.comment.body}</p>
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