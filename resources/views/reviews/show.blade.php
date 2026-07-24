<x-layout>
    <x-slot:title>Laravel from scratch - 2026 Edition</x-slot:title>
    <div class="px-50">
        <section class="py-10">
            <nav class="flex flex-row justify-between">
                <x-nav-link href="/"
                    class="flex flex-row gap-3 font-bold text-[.90rem] items-center"><x-heroicon-s-arrow-small-left
                        class="size-5" />Back to Reviews</x-nav-link>
                <div class="flex flex-row gap-3">
                    @if(!empty($review))
                        @can('update', $review)
                            <x-badge-link type="link"><x-heroicon-s-arrow-top-right-on-square class="size-3.5" />Edit Icon</x-badge-link>
                        @endcan
                        @can('delete', $review)
                            <x-badge-link for="my_modal_7" type="label" class="text-red-500 cursor-pointer btn"><x-heroicon-s-trash
                        class="size-3.5" />Delete</x-badge-link> @endcan
                    @endif
                </div>
            </nav>
        </section>
        <section class="flex flex-col">
            <div class="max-h-100 overflow-hidden rounded-md">
                <img src="{{ url($game['cover']) }}" alt="{{ $review->game->title . ' cover' }}" class="w-full h-full object-contain">
            </div>
            <div class="py-8 px-3 flex flex-col gap-3">
                <h1 class="text-4xl font-bold">{{ $review->title }}</h1>
                <div class="flex flex-col gap-6">
                    <ul class="flex flex-row gap-5">
                        <li><x-badge-link href="/" type="link" class="bg-[#033317] text-accent rounded-[10px] px-1 py-0 font-normal">{{ $review->recommendation->label() }}</x-badge-link></li>
                        <li class="flex flex-row items-center gap-1"><x-heroicon-o-calendar class="size-4.5" />Publicado em {{ $review->getPublishedDate() }}</li>
                        <li class="flex flex-row items-center gap-1"><x-heroicon-s-arrow-path class="size-4.5" />Atualizado em {{ $review->getUpdatedDate() }}</li>
                        <li class="flex flex-row items-center gap-1"><x-heroicon-o-user class="size-4.5" />Por {{ $review->user->name }}</li>
                        <li><a href="#comments" class="flex flex-row items-center gap-1"><x-heroicon-o-chat-bubble-oval-left class="size-4.5" />{{ count($review->comments) }} comentários</a></li>
                    </ul>
                    <ul class="flex flex-row flex-wrap gap-4 bg-[#181818] rounded-2xl p-4">
                        <li class="w-[calc(50%-0.5rem)] ">Plataforma(s)<br><strong>{{ $game['platforms'] }}</strong></li>
                        <li class="w-[calc(50%-0.5rem)] ">Status<br><strong>Campanha Concluíd</strong>a</li>
                        <li class="w-[calc(50%-0.5rem)] ">Lançamento<br><strong>{{ $game['release_date'] }}</strong></li>
                        <li class="w-[calc(50%-0.5rem)] ">Nota<br><strong>{{ $review->rating === '10.0' ? 10 : $review->rating }}/10</strong></li>
                    </ul>
                    <h2 class="font-bold">Resumo</h2>
                    <p>{{ $game['summary'] }}</p>
                </div>
              <hr class="my-8 border-t border-white/8" />
                <article class="prose prose-invert max-w-none">
                    <h2 class="font-bold mb-5">Review</h2>
                    {!! nl2br(e($review->body)) !!}

                </article>
                <div class="flex flex-col gap-4 mt-8">
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-4">
                        <h3 class="font-bold flex flex-row gap-3 items-center"><x-heroicon-o-hand-thumb-up class="size-4.5 text-primary" />Pontos positivos</h3>
                        <ul class="list-disc pl-8">
                            <li>Exploração</li>
                            <li>Direção de arte absurda</li>
                            <li>Chfes memoráveis</li>
                        </ul>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-4">
                       <h3 class="font-bold flex flex-row gap-3 items-center"><x-heroicon-o-hand-thumb-down class="size-4.5 text-warning" />Pontos negativos</h3>
                        <ul class="list-disc pl-8">
                            <li>Exploração</li>
                            <li>Direção de arte absurda</li>
                            <li>Chfes memoráveis</li>
                        </ul>
                    </div>
                </div>
                <hr class="mt-8 mb-5 border-t border-white/8" />
            </div>
        </section>
        <section id="comments" class="flex flex-col px-3">
            <h2 class="font-bold text-[1.3rem]">Comentários <span>({{ count($review->comments)}})</span></h2>
            @if(!empty($review->comments))
                <ul class="flex flex-col gap-3">
                    @foreach($review->comments as $comment)
                        <li class="flex flex-row gap-4 border-b border-white/8 py-8">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}" alt="User"
                                class="rounded-[50%] h-12">
                            <div class="w-full">
                                <span class="flex flex-row justify-between mb-3">
                                    <h3 class="font-bold">{{ $comment->user->name }}</h3>
                                    <h4 class="label text-[.90rem]">há 2 horas</h4>
                                </span>
                                <p>{{ $comment->body }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
    <!-- Delete Review Confirmation Modal -->
    <input type="checkbox" id="my_modal_7" class="modal-toggle" />
    <div class="modal" role="dialog">
        <div class="modal-box px-4 py-2 bg-[#181818]">
            <div class="flex flex-row justify-between items-center">
                <h3 class="text-lg font-bold">Delete Review</h3>
                 <label class="btn font-bold bg-transparent shadow-none border-0 outline-0 m-0 p-0" for="my_modal_7"><x-heroicon-o-x-mark class="size-4.5" /></label>
            </div>
            <p class="py-4 text-center text-[1.2rem] my-10">Are you sure you want to delete this review?</p>
            <div class="flex flex-row justify-between gap-3 mb-2">
                <label class="btn font-bold flex-1/2 bg-[#030303]" for="my_modal_7">CANCEL</label>
                <button type="submit" form="delete-review" class="btn font-bold flex-1/2 bg-red-500">DELETE</button>
            </div>
        </div>
    </div>
    <!-- Delete Review Form -->
    <form method="POST" action="/reviews/{{ $review->id }}" id="delete-review" class="hidden">@csrf @method('DELETE')</form>
</x-layout>