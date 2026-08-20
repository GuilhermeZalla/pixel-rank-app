<x-layout>
    <x-slot:title>Create Review</x-slot:title>
    <x-form.form method="POST" action="/reviews" class="m-[6vh] w-[50%]">
        @csrf
        <div class="flex flex-col gap-5">
            <x-form.field class="text-center mb-1">
                <x-form.description title="Create Review"
                    description="Share your opinion on a game!"></x-form.description>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2 relative">
                <x-form.label for="game">Game Name</x-form.label>
                <x-form.input type="search" />
            </x-form.field>
            <x-form.field class="flex flex-col gap-1">
                <x-form.label for="title">Review Title</x-form.label>
                <x-form.input type="text" name="title" id="title" placeholder="Your review title" required maxlength="120"/>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="recommendation">Your Recommendation and rating</x-form.label>
                <div class="flex flex-row justify-between gap-3">
                    <div class="flex flex-row flex-wrap gap-3 flex-5/6">
                        <x-form.button type="radio" option="recommended" required>Recommended</x-form.button>
                        <x-form.button type="radio" option="not_recommended">Not Recommended</x-form.button>
                        <x-form.button type="radio" option="mixed">Mixed</x-form.button>
                        <x-form.button type="radio" option="essential">Essential</x-form.button>
                    </div>
                    <div class="border border-[#8888884A] rounded-[5px] bg-[#0E0E0E] flex flex-col items-center justify-center flex-1/6">
                        <x-form.label for="rating" class="h-full">Score</x-form.label>
                        <input type="number" name="rating" id="rating" min="0" max="10" step="0.5" placeholder="0 - 10"
                            class="input bg-transparent border-none outline-none pb-5 text-[1.2rem] font-bold text-white w-full text-center focus:border-none focus:outline-none placeholder:text-white" />
                    </div>
                </div>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="review">Write Your Review</x-form.label>
                <x-form.input type="textarea" name="body" id="review" placeholder="Write your review!" minlength="150" rows="15" value="{{ !empty($review) ? $review : '' }}" />
            </x-form.field>
            <x-form.field class="flex flex-row gap-2">
                <x-form.input type="checkbox" name="contains_spoilers" id="spoiler"/>
                <x-form.label for="spoiler" class="cursor-pointer">This review contains spoilers</x-form.label>
            </x-form.field>
            <x-form.field class="flex flex-col gap-4 my-4">
                <x-form.label for="proscons">The Good & The Bad (max 10 for both)</x-form.label>
                <div class="flex flex-row justify-between gap-4">
                    <div class="flex flex-col flex-wrap gap-4 bg-[#0E0E0E] rounded-2xl p-4 w-full">
                         <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]"><x-heroicon-o-hand-thumb-up
                                class="size-4 text-primary" />Pontos positivos</h3>
                        <div class="flex gap-2">
                            <input type="text" id="pro-input" class="input input-bordered flex-1 font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E"
                                placeholder="Add a positive point..." maxlength="50" name="proscons">
                            <button type="button" id="add-pro" class="btn btn-primary">Add</button>
                        </div>
                        <ul id="pros-list" class="space-y-2"></ul>
                        <p id="pros-empty" class="text-sm text-gray-400">Nenhum ponto adicionado ainda.</p>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-[#0E0E0E] rounded-2xl p-4 w-full">
                        <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]">
                            <x-heroicon-o-hand-thumb-down class="size-4 text-warning" /> Pontos negativos
                        </h3>
                        <div class="flex gap-2">
                            <input type="text" id="cons-input"
                                class="input input-bordered flex-1 font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E"
                                placeholder="Add a negative point..." maxlength="50" name="proscons">
                            <button type="button" id="add-cons" class="btn btn-warning text-white">Add</button>
                        </div>
                        <ul id="cons-list" class="space-y-2"></ul>
                        <p id="cons-empty" class="text-sm text-gray-400">Nenhum ponto adicionado ainda.</p>
                    </div>
                </div>
            </x-form.field>
            <x-form.field>
                <x-form.label for="platform">Platform Played</x-form.label>
                <x-form.select name="platform_playable">
                    @foreach($platforms as $platform)
                        <option value="{{ $platform['name'] }}" class="hover:bg-accent hover:text-black">{{ $platform['name'] }}</option>
                    @endforeach
                </x-form.select>
            </x-form.field>
            <x-form.field class="mt-2 flex-row justify-end gap-3">
                <x-form.link href="/" class="w-35 btn">Return</x-form.link>
                <x-form.button class="w-35">Create</x-form.button>
            </x-form.field>
        </div>
    </x-form.form>
    <script>
        const searchInput = document.getElementById('game-search');
        const dropdown = document.getElementById('game-dropdown');
        const gameId = document.getElementById('game-id');
        const gameName = document.getElementById('game-name');
        const gameCover = document.getElementById('game-cover');

        let timeout = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);

            gameId.value = '';
            gameName.value = '';
            gameCover.value = '';

            const query = this.value.trim();

            if (!query) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
                return;
            }

            timeout = setTimeout(async () => {
                const response = await fetch(`/games/search?query=${query}`);
                const games = await response.json();

                dropdown.innerHTML = '';

                games.forEach(game => {
                    const item = document.createElement('div');

                    item.textContent = game.name;
                    item.classList.add('cursor-pointer', 'hover:bg-accent', 'hover:text-[#0E0E0E]', 'rounded-[5px]', 'p-2', 'text-[.85rem]');

                    item.addEventListener('click', () => {
                        searchInput.value = game.name;
                        gameId.value = game.id;
                        gameCover.value = game.cover.image_id;
                        gameName.value = game.name;

                        dropdown.classList.add('hidden');
                    });

                    dropdown.appendChild(item);
                });

                dropdown.classList.remove('hidden');

            }, 300);
        });

        document.addEventListener('click', function (event) {
                if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });

        const rating = document.getElementById('rating');

            rating.addEventListener('blur', function () {
                if (this.value === '') return;

                const value = this.value.replace(',', '.');
                const regex = /^(10(\.0)?|[0-9](\.[0-9])?)$/;

                if (!regex.test(value)) {
                    alert('The score must be between 0 and 10 with at most one decimal place.');
                    this.value = '';
                    this.focus();
                }
            });

        // Add positive & negative points

        setupDynamicList({
            input: 'pro-input',
            button: 'add-pro',
            list: 'pros-list',
            empty: 'pros-empty',
            field: 'pros[]'
        });

        setupDynamicList({
            input: 'cons-input',
            button: 'add-cons',
            list: 'cons-list',
            empty: 'cons-empty',
            field: 'cons[]'
        });

        function setupDynamicList({ input, button, list, empty, field }) {

            const MAX_ITEMS = 10;

            const inputElement = document.getElementById(input);
            const buttonElement = document.getElementById(button);
            const listElement = document.getElementById(list);
            const emptyElement = document.getElementById(empty);

            buttonElement.addEventListener('click', addItem);

            inputElement.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addItem();
                }
            });

            function addItem() {

                if (listElement.children.length >= MAX_ITEMS) {
                    return;
                }

                const value = inputElement.value.trim();

                if (!value) {
                    return;
                }

                emptyElement?.classList.add('hidden');

                const li = document.createElement('li');
                li.classList.add(
                    'flex',
                    'justify-between',
                    'items-center',
                    'bg-base-200',
                    'rounded-lg',
                    'px-3',
                    'py-2'
                );

                const span = document.createElement('span');
                span.textContent = value;

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = field;
                hidden.value = value;

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.textContent = '✕';
                removeButton.classList.add(
                    'btn',
                    'btn-xs',
                    'btn-error'
                );

                removeButton.addEventListener('click', function () {

                    li.remove();

                    if (listElement.children.length === 0) {
                        emptyElement?.classList.remove('hidden');
                    }

                    if (listElement.children.length < MAX_ITEMS) {
                        buttonElement.disabled = false;
                    }

                });

                li.append(
                    span,
                    removeButton,
                    hidden
                );

                listElement.appendChild(li);

                if (listElement.children.length >= MAX_ITEMS) {
                    buttonElement.disabled = true;
                }

                inputElement.value = '';
                inputElement.focus();
            }
        }
    </script>
</x-layout>