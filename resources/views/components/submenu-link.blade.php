@props(['type' => 'a'])

@if($type != 'a')
<li class="font-bold"><button type="submit" form="logout-form">Log Out</button><form method="POST" action="/logout" class="hidden" id="logout-form">@csrf @method('DELETE')</form></li>
@else
    <li class="font-bold hover:bg-accent hover:text-black rounded-md"><a href="{{ $attributes->get('href') }}">{{ $slot }}</a></li>
@endif
