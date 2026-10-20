<x-layout>
    <x-slot:title>{{ __('reviews.create_title') }}</x-slot:title>
    <x-form.form method="POST" action="/reviews" class="mx-auto my-[6vh] w-[calc(100%-2rem)] max-w-4xl border border-[#88888833] bg-[#0E0E0E] rounded-[20px] px-4 py-6 sm:px-8 sm:py-8 lg:w-1/2">
        @csrf
        <div class="flex flex-col gap-5">
            <x-form.field class="text-center mb-1">
                <x-form.description :title="__('reviews.create_heading')"
                    :description="__('reviews.create_description')"></x-form.description>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2 relative">
                <x-form.label for="game-search">{{ __('reviews.game_name') }}</x-form.label>
                <x-form.input type="search" />
                <p id="game-search-status" class="text-sm text-warning hidden" role="status" aria-live="polite"></p>
            </x-form.field>
            <x-form.field class="flex flex-col gap-1">
                <x-form.label for="title">{{ __('reviews.review_title') }}</x-form.label>
                <x-form.input type="text" name="title" id="title" :placeholder="__('reviews.review_title_placeholder')" required maxlength="120"/>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="recommendation">{{ __('reviews.recommendation_rating') }}</x-form.label>
                <div class="flex flex-col justify-between gap-3 md:flex-row">
                    <div class="flex flex-row flex-wrap gap-3 md:flex-5/6">
                        <x-form.button type="radio" option="recommended" required>{{ __('reviews.recommendations.recommended') }}</x-form.button>
                        <x-form.button type="radio" option="not_recommended">{{ __('reviews.recommendations.not_recommended') }}</x-form.button>
                        <x-form.button type="radio" option="mixed">{{ __('reviews.recommendations.mixed') }}</x-form.button>
                        <x-form.button type="radio" option="essential">{{ __('reviews.recommendations.essential') }}</x-form.button>
                    </div>
                    <div class="flex w-full flex-col items-center justify-center rounded-[5px] border border-[#8888884A] bg-[#0E0E0E] md:flex-1/6">
                        <x-form.label for="rating" class="h-full">{{ __('reviews.score') }}</x-form.label>
                        <input type="number" name="rating" id="rating" min="0" max="10" step="0.5" placeholder="{{ __('reviews.score_placeholder') }}"
                            class="input bg-transparent border-none outline-none pb-5 text-[1.2rem] font-bold text-white w-full text-center focus:border-none focus:outline-none placeholder:text-white" />
                    </div>
                </div>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="review">{{ __('reviews.write_your_review') }}</x-form.label>
                <x-form.input type="textarea" name="body" id="review" :placeholder="__('reviews.review_body_placeholder')" minlength="150" rows="15" value="{{ !empty($review) ? $review : '' }}" />
            </x-form.field>
            <x-form.field class="flex flex-row gap-2">
                <x-form.input type="checkbox" name="contains_spoilers" id="spoiler"/>
                <x-form.label for="spoiler" class="cursor-pointer">{{ __('reviews.contains_spoilers') }}</x-form.label>
            </x-form.field>
            <x-form.field class="flex flex-col gap-4 my-4">
                <x-form.label for="proscons">{{ __('reviews.good_and_bad') }}</x-form.label>
                <div class="flex flex-col justify-between gap-4 md:flex-row">
                    <div class="flex flex-col flex-wrap gap-4 bg-[#14181C] rounded-2xl p-4 w-full">
                         <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]"><x-heroicon-o-hand-thumb-up
                                class="size-4 text-primary" />{{ __('reviews.positive_points') }}</h3>
                <div class="flex min-w-0 flex-wrap gap-2 sm:flex-nowrap">
                            <input type="text" id="pro-input" class="input input-bordered flex-1 font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E]"
                                placeholder="{{ __('reviews.positive_point_placeholder') }}" maxlength="50" name="proscons">
                            <button type="button" id="add-pro" class="btn btn-primary">{{ __('reviews.add') }}</button>
                        </div>
                        <ul id="pros-list" class="space-y-2"></ul>
                        <p id="pros-empty" class="text-sm text-gray-400">{{ __('reviews.no_points_yet') }}</p>
                    </div>
                    <div class="flex flex-col flex-wrap gap-4 bg-[#14181C] rounded-2xl p-4 w-full">
                        <h3 class="font-bold flex flex-row gap-3 items-center text-[.90rem]">
                            <x-heroicon-o-hand-thumb-down class="size-4 text-warning" /> {{ __('reviews.negative_points') }}
                        </h3>
                        <div class="flex min-w-0 flex-wrap gap-2 sm:flex-nowrap">
                            <input type="text" id="cons-input"
                                class="input input-bordered flex-1 font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E]"
                                placeholder="{{ __('reviews.negative_point_placeholder') }}" maxlength="50" name="proscons">
                            <button type="button" id="add-cons" class="btn btn-warning text-white">{{ __('reviews.add') }}</button>
                        </div>
                        <ul id="cons-list" class="space-y-2"></ul>
                        <p id="cons-empty" class="text-sm text-gray-400">{{ __('reviews.no_points_yet') }}</p>
                    </div>
                </div>
            </x-form.field>
            <x-form.field>
                <x-form.label for="platform">{{ __('reviews.platform_played') }}</x-form.label>
                <x-form.select name="platform_playable">
                    @if (empty($platforms))
                        <option value="{{ __('reviews.not_available') }}" class="hover:bg-accent hover:text-black">{{ __('reviews.not_available') }}</option>
                    @else
                        @foreach($platforms as $platform)
                            <option value="{{ $platform['name'] }}" class="hover:bg-accent hover:text-black">{{ $platform['name'] }}</option>
                        @endforeach
                    @endif
                </x-form.select>
                @unless ($gameApiAvailable)
                    <p class="text-sm text-warning mt-1">{{ __('reviews.game_api_unavailable') }}</p>
                @endunless
            </x-form.field>
            <x-form.field class="mt-2 flex-col justify-end gap-3 sm:flex-row">
                <x-form.link href="/" class="btn w-full sm:w-35">{{ __('reviews.return') }}</x-form.link>
                <x-form.button class="w-full sm:w-35">{{ __('reviews.create') }}</x-form.button>
            </x-form.field>
        </div>
    </x-form.form>
    <script>
        const searchInput = document.getElementById('game-search');
        const dropdown = document.getElementById('game-dropdown');
        const gameId = document.getElementById('game-id');
        const gameName = document.getElementById('game-name');
        const gameCover = document.getElementById('game-cover');
        const searchStatus = document.getElementById('game-search-status');

        const showSearchStatus = (message) => {
            if (!searchStatus) return;
            searchStatus.textContent = message;
            searchStatus.classList.toggle('hidden', !message);
        };

        let timeout = null;
        let activeOptionIndex = -1;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            activeOptionIndex = -1;
            dropdown.innerHTML = '';
            dropdown.classList.add('hidden');
            searchInput.setAttribute('aria-expanded', 'false');
            searchInput.removeAttribute('aria-activedescendant');

            gameId.value = '';
            gameName.value = '';
            gameCover.value = '';

            const query = this.value.trim();

            showSearchStatus('');

            if (!query) {
                dropdown.innerHTML = '';
                dropdown.classList.add('hidden');
                searchInput.setAttribute('aria-expanded', 'false');
                searchInput.removeAttribute('aria-activedescendant');
                return;
            }

            timeout = setTimeout(async () => {
                let games;

                try {
                    const response = await fetch(`/games/search?query=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Game search request failed');
                    games = await response.json();
                } catch (error) {
                    dropdown.classList.add('hidden');
                    searchInput.setAttribute('aria-expanded', 'false');
                    showSearchStatus(@js(__('reviews.game_search_failed')));
                    return;
                }

                if (!Array.isArray(games)) {
                    dropdown.classList.add('hidden');
                    searchInput.setAttribute('aria-expanded', 'false');
                    showSearchStatus(@js(__('reviews.game_search_failed')));
                    return;
                }

                dropdown.innerHTML = '';
                activeOptionIndex = -1;

                games.forEach((game, index) => {
                    const item = document.createElement('div');

                    item.textContent = game.name;
                    item.id = `game-option-${index}`;
                    item.setAttribute('role', 'option');
                    item.setAttribute('aria-selected', 'false');
                    item.classList.add('cursor-pointer', 'hover:bg-accent', 'hover:text-[#0E0E0E]', 'rounded-[5px]', 'p-2', 'text-[.85rem]');

                    item.addEventListener('click', () => {
                        searchInput.value = game.name;
                        gameId.value = game.id;
                        gameCover.value = game.cover?.image_id ?? '';
                        gameName.value = game.name;

                        dropdown.classList.add('hidden');
                        searchInput.setAttribute('aria-expanded', 'false');
                        searchInput.removeAttribute('aria-activedescendant');
                    });

                    dropdown.appendChild(item);
                });

                if (games.length > 0) {
                    dropdown.classList.remove('hidden');
                    searchInput.setAttribute('aria-expanded', 'true');
                } else {
                    dropdown.classList.add('hidden');
                    searchInput.setAttribute('aria-expanded', 'false');
                }

            }, 300);
        });

        searchInput.addEventListener('keydown', function (event) {
            const options = Array.from(dropdown.querySelectorAll('[role="option"]'));

            if (event.key === 'ArrowDown' && options.length > 0) {
                event.preventDefault();
                activeOptionIndex = (activeOptionIndex + 1) % options.length;
            } else if (event.key === 'ArrowUp' && options.length > 0) {
                event.preventDefault();
                activeOptionIndex = activeOptionIndex <= 0 ? options.length - 1 : activeOptionIndex - 1;
            } else if (event.key === 'Enter' && activeOptionIndex >= 0) {
                event.preventDefault();
                options[activeOptionIndex]?.click();
                return;
            } else if (event.key === 'Escape') {
                dropdown.classList.add('hidden');
                searchInput.setAttribute('aria-expanded', 'false');
                searchInput.removeAttribute('aria-activedescendant');
                return;
            } else {
                return;
            }

            options.forEach((option, index) => {
                const active = index === activeOptionIndex;
                option.setAttribute('aria-selected', String(active));
                option.classList.toggle('bg-accent', active);
                option.classList.toggle('text-[#0E0E0E]', active);
            });
            searchInput.setAttribute('aria-activedescendant', options[activeOptionIndex].id);
        });

        document.addEventListener('click', function (event) {
                if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                    searchInput.setAttribute('aria-expanded', 'false');
                }
            });

        const rating = document.getElementById('rating');

            rating.addEventListener('blur', function () {
                if (this.value === '') return;

                const value = this.value.replace(',', '.');
                const regex = /^(10(\.0)?|[0-9](\.[0-9])?)$/;

                if (!regex.test(value)) {
                    alert(@js(__('reviews.rating_invalid')));
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
