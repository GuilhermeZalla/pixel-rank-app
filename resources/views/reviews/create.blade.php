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
                <x-form.input type="title" name="title" id="title" placeholder="Your review title" required maxlength="120"/>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="recommendation">Your Recommendation</x-form.label>
                <div class="flex flex-row flex-wrap gap-3">
                    <x-form.button type="radio" option="recommended" required>Recommended</x-form.button>
                    <x-form.button type="radio" option="not_recommended">Not Recommended</x-form.button>
                    <x-form.button type="radio" option="mixed">Mixed</x-form.button>
                    <x-form.button type="radio" option="essential">Essential</x-form.button>
                </div>
            </x-form.field>
            <x-form.field>
                <x-form.label for="rating">Your rating</x-form.label>
                <x-form.input type="number" />
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="review">Write Your Review</x-form.label>
                <x-form.input type="textarea" name="body" id="review" placeholder="Write your review!" minlength="150" rows="15" value="{{ !empty($review) ? $review : '' }}" />
            </x-form.field>
            <x-form.field class="flex flex-row gap-2">
                <x-form.input type="checkbox" name="spoiler" id="spoiler"/>
                <x-form.label for="spoiler" class="cursor-pointer">This review contains spoilers</x-form.label>
            </x-form.field>
            <div class="flex flex-col gap-4 my-4">
                <x-form.label for="title">The Good & The Bad (max 10 for both)</x-form.label>
                <div class="flex flex-row justify-between gap-4">
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-4 w-full">
                        <div class="flex gap-2">
                            <input type="text" id="pro-input" class="input input-bordered flex-1"
                                placeholder="Add a positive point..." maxlength="100">
                            <button type="button" id="add-pro" class="btn btn-primary">Add</button>
                        </div>
                        <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]"><x-heroicon-o-hand-thumb-up
                                class="size-4 text-primary" />Pontos positivos</h3>
                        <ul id="pros-list" class="space-y-2"></ul>
                        <p id="pros-empty" class="text-sm text-gray-400">Nenhum ponto positivo.</p>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-[#181818] rounded-2xl p-4 w-full">
                        <div class="flex gap-2">
                            <input type="text" id="cons-input" class="input input-bordered flex-1"
                                placeholder="Add a negative point..." maxlength="100">
                            <button type="button" id="add-cons" class="btn btn-primary">Add</button>
                        </div>
                        <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]">
                            <x-heroicon-o-hand-thumb-down class="size-4 text-warning" /> Pontos negativos</h3>
                        <ul id="cons-list" class="space-y-2"></ul>
                        <p id="cons-empty" class="text-sm text-gray-400">Nenhum ponto negativo.</p>
                    </div>
                </div>
            </div>
            <x-form.field class="mt-2">
                <x-form.button>Return</x-form.button>
                <x-form.button>Create</x-form.button>
            </x-form.field>
        </div>
    </x-form.form>
    <script>
        const searchInput = document.getElementById('game-search');
        const dropdown = document.getElementById('game-dropdown');
        const gameId = document.getElementById('game-id');

        let timeout = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);

            gameId.value = '';

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

            rating.addEventListener('input', function () {

                if (this.value === '') {
                    return;
                }

                let value = Number(this.value);

                if (!Number.isNaN(value)) {
                    if (value < 0) {
                        this.value = 0;
                    } else if (value > 10) {
                        this.value = 10;
                    }
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