<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'pt_BR' ? 'pt-BR' : 'en-US' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('errors.pages.'.($status ?? 500).'.title') }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 1rem; box-sizing: border-box; font-family: system-ui, sans-serif; color: #222; background: #f4f4f4; }
        main { width: min(100%, 36rem); padding: 2.5rem 1.5rem; box-sizing: border-box; text-align: center; background: #fff; border-radius: .75rem; }
        .code { margin: 0; font-size: clamp(3rem, 12vw, 5rem); font-weight: 700; }
        h1 { margin: 1rem 0; font-size: clamp(1.5rem, 6vw, 2rem); }
        a { display: inline-block; margin-top: 1rem; padding: .75rem 1rem; border-radius: .4rem; color: #fff; background: #4f46e5; text-decoration: none; }
        a:focus-visible { outline: 3px solid #222; outline-offset: 3px; }
    </style>
</head>
<body>
    <main>
        <p class="code">{{ $status ?? 500 }}</p>
        <h1>{{ __('errors.pages.'.($status ?? 500).'.title') }}</h1>
        <p>{{ __('errors.pages.'.($status ?? 500).'.message') }}</p>
        <a href="/">{{ __('errors.home') }}</a>
    </main>
</body>
</html>
