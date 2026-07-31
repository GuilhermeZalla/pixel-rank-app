<!DOCTYPE html>
<html lang="en" data-theme="pixelrank" class="text-base-content">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Site Content -->
<body class="pb-20">
    <div
        class="navbar flex flex-row justify-between bg-base-100 shadow-sm border-b border-b-[#88888833] px-4 sm:px-0 md:px-40 lg:px-20">
        <div> <a class="btn btn-ghost text-xl" href="/"> <img src="{{ asset('pixelrank.png') }}"
                    alt="PixelRank" class="w-full h-full object-contain"></a>
        </div>
        <label class="input w-120" class="rounded-[7px]">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-[1.7em] opacity-50" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12c0-2.21 1.79-4 4-4h12a4 4 0 0 1 4 4v3a3 3 0 0 1-3 3h-1l-2-2h-8l-2 2H5a3 3 0 0 1-3-3v-3z" />
                <path d="M6 12h4" />
                <path d="M8 10v4" />
                <path d="M15 11h.01" />
                <path d="M18 13h.01" />
            </svg>
            <input type="game" required placeholder="Search games..." />
        </label>
        @auth
            <div>
                <ul class="menu menu-horizontal px-1">
                    <li><a>Edit Profile</a></li>
                    <li>
                        <details>
                            <summary>Parent</summary>
                            <ul class="bg-base-100 rounded-t-none p-2">
                                <li><a>Link 1</a></li>
                                <li> <button type="submit" form="logout-form">Log Out</button>
                                    <form method="POST" action="/logout" class="hidden" id="logout-form">@csrf
                                        @method('DELETE')</form>
                                </li>
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
                         <li><x-nav-link href="/register" class="bg-primary text-primary-content font-bold rounded-md">Register</x-nav-link></li>
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