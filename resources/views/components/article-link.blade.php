 <a href="/reviews/{{ $review->id }}"  class="sm:w-full md:flex-1/4 lg:flex-1/4 border-2 border-[#88888822] rounded-[7px] hover:border-primary transition-all duration-300 ease-in-out">
    <article class="rounded-[7px]">
        <div class="flex flex-col">
            <div class="h-50 overflow-hidden rounded-tr-md rounded-tl-md">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTmbdujr8xhGqABB81Nt4VjhM8a_GlRee7rI6MjmNr_0n_gH4uEuMIUuy3p&s=10"
                    alt="Placeholder Image" class="w-full h-full object-cover">
            </div>
            <div class="py-4 px-3 flex flex-col gap-5">
                <div class="flex flex-col gap-2 w-3/4">
                    <h2 class="font-bold text-lg">{{ $review->title }}</h2>
                    <div class="flex flex-row gap-2">
                        <span class="badge badge-outline font-bold text-[.72rem]">Tag 1</span>
                        <span class="badge badge-outline font-bold text-[.72rem]">Tag 2</span>
                    </div>
                </div>
                    <p class="text-sm text-gray-400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor
                        incididunt ut labore et dolore magna aliqua.</p>
                    <span class="text-xs text-gray-500 mt-7">2 hours ago</span>
            </div>
        </div>
    </article>
</a>