@if ($paginator->hasPages())

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8">

    {{-- Results count --}}
    <p class="text-sm text-gray-400">
        Showing
        <span class="font-medium text-white">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-medium text-white">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-medium text-white">{{ $paginator->total() }}</span>
        results
    </p>


    <div class="flex items-center gap-2">


        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())

            <span class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-600 cursor-not-allowed">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </span>

        @else

            <a href="{{ $paginator->previousPageUrl() }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-300 hover:text-green-400 hover:border-green-500 transition">

                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>

            </a>

        @endif



        {{-- Pagination Elements --}}
        @foreach ($elements as $element)

            {{-- Dots --}}
            @if (is_string($element))

                <span class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-500">
                    {{ $element }}
                </span>

            @endif



            {{-- Pages --}}
            @if (is_array($element))

                @foreach ($element as $page => $url)

                    @if ($page == $paginator->currentPage())

                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] bg-green-500 text-black font-semibold">
                            {{ $page }}
                        </span>

                    @else

                        <a href="{{ $url }}"
                           class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-300 hover:bg-[#1A1A1A] hover:text-green-400 hover:border-green-500 transition">

                            {{ $page }}

                        </a>

                    @endif

                @endforeach

            @endif

        @endforeach



        {{-- Next Page --}}
        @if ($paginator->hasMorePages())

            <a href="{{ $paginator->nextPageUrl() }}"
               class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-300 hover:text-green-400 hover:border-green-500 transition">

                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>

            </a>

        @else

            <span class="inline-flex items-center justify-center w-10 h-10 rounded-[10px] border border-[#88888833] text-gray-600 cursor-not-allowed">

                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>

            </span>

        @endif


    </div>

</div>

@endif