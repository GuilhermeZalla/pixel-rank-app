<!DOCTYPE html>
<html lang="en" data-theme="pixelrank" class="text-base-content">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="navbar bg-base-100 shadow-sm border-b border-b-[#88888833] px-30">
        <div class="flex-1">
            <a class="btn btn-ghost text-xl">daisyUI</a>
        </div>
        @auth
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1 flex flex-justify-between gap-2">
                    <li><a href="/profile">Edit Profile</a></li>
                    <li>
                        <button type="submit" form="logout-form">Log Out</button>
                        <form method="POST" action="/logout" class="hidden" id="logout-form">@csrf @method('DELETE')</form>
                    </li>
                </ul>
            </div>
        @endauth
        @guest
            <div class="flex-none">
                <ul class="menu menu-horizontal px-1 flex flex-justify-between gap-2">
                    <li><a href="/login">Sign In</a></li>
                    <li><a href="/register" class="bg-primary text-primary-content font-bold rounded-md">Register</a></li>
                </ul>
            </div>
        @endguest
    </div>
    <main class="flex flex-col px-30">
        {{ $slot }}
    </main>
</body>

</html>
