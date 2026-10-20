@props(['title', 'description'])

<h1 {{ $attributes->merge(['class' => 'font-bold text-[1.8rem]']) }}>{{ $title }}</h1>
<p>{{ $description }}</p>