@props(['name'])

@error($name)
    <p class="text-red-500 text-[.85rem]">{{ $message }}</p>
@enderror