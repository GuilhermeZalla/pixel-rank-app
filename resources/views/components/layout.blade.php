<!DOCTYPE html>
<html lang="en" data-theme="pixelrank" class="text-base-content bg-base-100">

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
    <div class="navbar flex flex-row justify-between bg-base-200 xl:px-10 items-center" style="box-shadow: 0px 2px 10px #0000009e;">
        <div class="flex flex-row"> <a class="text-xl" href="/"><img src="{{ asset('pixelrank.png') }}"
                    alt="PixelRank" class="object-contain h-full w-40"></a>
        </div>
         @if(!request()->is('users/*'))
            <form method="GET" action="/search/popular" class="relative">
                <label class="input w-120 rounded-[7px]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-[2.5em] opacity-50" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 12c0-2.21 1.79-4 4-4h12a4 4 0 0 1 4 4v3a3 3 0 0 1-3 3h-1l-2-2h-8l-2 2H5a3 3 0 0 1-3-3v-3z" />
                        <path d="M6 12h4" />
                        <path d="M8 10v4" />
                        <path d="M15 11h.01" />
                        <path d="M18 13h.01" />
                    </svg>
                    <input type="search" name="search" placeholder="Search for a review..." autocomplete="on"
                        class="p-2.5 pl-1 text-[.90rem] font-bold border-none focus:bg-transparent focus:outline-none w-120"
                        required>
                </label>
            </form>
        @endif
        @auth
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1">
                    <li>
                        <details>
                            <summary class="font-bold"> <img @if(!empty(auth()->user()->avatar)) src="{{ asset(auth()->user()->avatar) }}" @else src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nickname) }}"@endif
                                alt="User" class="rounded-[50%] h-9">{{ auth()->user()->nickname }}</summary>
                            <ul class="rounded-lg border border-[#88888833] bg-base-200 p-2 mt-1 left-12 w-40 z-50">
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/menu1/edit">Profile</x-submenu-link>
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/menu2/edit">My Reviews</x-submenu-link>
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/menu3/edit">My Comments</x-submenu-link>
                                <x-submenu-link href="{{ route('reviews.create') }}">New Review</x-submenu-link>
                                <x-submenu-link href="/users/{{ auth()->user()->id }}/menu4/edit">Settings</x-submenu-link>
                                <x-submenu-link href="{{ route('reviews') }}">Home</x-submenu-link>
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
    </script>
</body>

</html>