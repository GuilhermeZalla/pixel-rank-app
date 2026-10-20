@props(['type' => 'submit', 'option' => 'recommended'])

@if($type === 'radio')
    <label class="w-full flex-1/3">
        <input id="{{ $option }}" type="radio" name="recommendation" class="peer sr-only" value="{{ $option }}">
        <span for="{{ $option }}" {{ $attributes->merge(['class' => 'block filter-input cursor-pointer rounded-[5px] border border-[#8888884A] bg-[#0E0E0E] p-2.5 text-center text-[.83rem] font-bold hover:border-accent peer-checked:bg-accent peer-checked:border-accent peer-checked:text-black']) }}>{{ $slot }}</span>
    </label>
@else
    <button type="submit" {{ $attributes->merge(['class' => 'btn btn-primary font-bold rounded-lg']) }}>{{ $slot }}</button>
@endif