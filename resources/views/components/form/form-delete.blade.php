
<form {{ $attributes->merge(['class' => 'hidden']) }}>@csrf @method('DELETE') {{ $slot }}</form>
