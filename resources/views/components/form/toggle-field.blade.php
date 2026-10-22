@props([
    'name',
    'title',
    'description' => null,
    'checked' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <input type="checkbox" {{ $attributes->merge(['class' => 'toggle toggle-primary', 'checked' => $checked ? 'checked' : null]) }} name="{{ $name }}" value="1" />
    <div class="flex-1 min-w-0">
        <label class="label cursor-pointer font-semibold text-base-content/90">{{ $title }}</label>
        @if($description)
            <label class="label text-xs text-base-content/60 mt-0.5">{{ $description }}</label>
        @endif
    </div>
    <x-form.error name="{{ $name }}" />
</div>
