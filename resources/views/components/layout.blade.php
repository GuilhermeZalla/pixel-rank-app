<!DOCTYPE html>
<html lang="en" data-theme="pixelrank" class="text-base-content">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Site Content -->
<body class="pb-20">
    <div class="navbar bg-base-100 shadow-sm border-b border-b-[#88888833] px-40">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl" href="/">daisyUI</a>
        </div>
        @auth
            <div class="flex-none">
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
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1 flex flex-justify-between gap-2">
                    <li><x-nav-link href="/login">Sign In</x-nav-link></li>
                    <li><x-nav-link href="/register"
                            class="bg-primary text-primary-content font-bold rounded-md">Register</x-nav-link></li>
                </ul>
            </div>
        @endguest
    </div>
    <main class="flex flex-col gap-10 px-40">
        {{ $slot }}
    </main>
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition.opacity.duration.300ms
            class="bg-primary font-bold px-4 py-3 rounded-lg fixed bottom-4 right-4">
            {{ session('success') }}
        </div>
    @endif
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