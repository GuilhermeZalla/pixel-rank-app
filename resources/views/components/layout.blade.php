<!DOCTYPE html>
<html lang="en" data-theme="pixelrank" class="text-base-content">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png/svg" href="{{ asset('pixelrank.svg') }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Site Content -->

<body class="pb-5">
    <div class="navbar flex flex-row justify-between bg-base-100 shadow-sm border-b border-b-[#88888833] px-10">
        <div class="flex flex-row"> <a class="text-xl" href="/"><img src="{{ asset('pixelrank.png') }}" alt="PixelRank"
                    class="object-contain w-30"></a>
        </div>
         @if(!request()->is('users/*'))
                <form method="GET" action="/" class="relative">
                    <label class="input w-120" class="rounded-[7px]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.8em] opacity-50" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M2 12c0-2.21 1.79-4 4-4h12a4 4 0 0 1 4 4v3a3 3 0 0 1-3 3h-1l-2-2h-8l-2 2H5a3 3 0 0 1-3-3v-3z" />
                            <path d="M6 12h4" />
                            <path d="M8 10v4" />
                            <path d="M15 11h.01" />
                            <path d="M18 13h.01" />
                        </svg>
                        <input type="text" id="game-search-layout" placeholder="Search a game..." autocomplete="on"
                            class="p-2.5 text-[.80rem] font-bold border-none focus:border-none focus:outline-none w-120" required>
                        <input type="hidden" name="game_id" id="game-id-layout">
                        <div id="game-dropdown-layout"
                            class="absolute top-12 left-0 z-99 flex flex-col gap-3 hidden overflow-y-scroll max-h-80 font-bold w-full border border-[#8888884A] focus:border-accent focus:outline-none rounded-[5px] bg-[#0E0E0E]">
                        </div>
                    </label>
                </form>
            @endif
        @auth
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li>
                        <details>
                            <summary> <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nickname) }}"
                                alt="User" class="rounded-[50%] h-7">{{ auth()->user()->nickname }}</summary>
                            <ul class="rounded-lg border border-[#88888833] bg-[#0E0E0E] p-2 mt-2 left-6 w-40">
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/{{ 'menu1' }}/edit">Profile</x-submenu-link>
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/{{ 'menu2' }}/edit">My Reviews</x-submenu-link>
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/{{ 'menu4' }}/edit">Settings</x-submenu-link>
                                <x-divisor></x-divisor>
                                <x-submenu-link type="submit"></x-submenu-link>
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
        @endauth
        @guest
            <div>
                <ul class="menu menu-horizontal px-1 flex flex-justify-between gap-2">
                    @if(!request()->is('login'))
                        <li><x-nav-link href="/login">Sign In</x-nav-link></li>
                    @endif
                    @if(!request()->is('register'))
                        <li><x-nav-link href="/register"
                                class="bg-primary text-primary-content font-bold rounded-md">Register</x-nav-link></li>
                    @endif
                </ul>
            </div>
        @endguest
    </div>
    <main class="flex flex-col gap-10">
        {{ $slot }}
    </main>
    @php
$flashTypes = [
    'success' => 'bg-primary',
    'info' => 'bg-blue-600',
];
    @endphp
    @foreach($flashTypes as $type => $color)
        @if(session($type))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                x-transition.opacity.duration.300ms class="{{ $color }} font-bold px-4 py-3 rounded-lg fixed bottom-4 right-4">
                {{ session($type) }}
            </div>
        @endif
    @endforeach
    <script>
        document.getElementById('review').addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.form.requestSubmit();
            }
        });

        const searchInput = document.getElementById('game-search-layout');
        const dropdown = document.getElementById('game-dropdown-layout');
        const gameId = document.getElementById('game-id-layout');

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
    </script>
</body>

</html>