@props(['targetMenu' => null])

<li role="presentation" class="w-full">
    <button type="button"
        role="tab"
        id="tab-{{ $targetMenu }}"
        aria-controls="panel-{{ $targetMenu }}"
        :aria-selected="activeMenu === '{{ $targetMenu }}' ? 'true' : 'false'"
        tabindex="0"
        {{ $attributes->merge([
            'class' => 'w-full text-left font-bold text-[.85rem] sm:text-[.90rem] min-h-[44px] py-2.5 px-3 sm:px-4 rounded-lg cursor-pointer flex items-center justify-between transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent focus-visible:ring-offset-2 focus-visible:ring-offset-[#0E0E0E] xl:border-l-3 xl:rounded-none xl:px-6'
        ]) }}>
        <span class="truncate">{{ $slot }}</span>
    </button>
</li>
