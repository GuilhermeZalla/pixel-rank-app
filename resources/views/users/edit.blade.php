<x-layout>
    <x-slot:title>Dashboard - {{ auth()->user()->nickname }}</x-slot:title>
    <div class="flex flex-row justify-between gap-8 mt-[10vh] mx-[2vw]" x-data="{ activeMenu: '{{ $menu }}' }">
        <section class="flex flex-col w-100 border border-[#88888833] bg-[#0E0E0E] rounded-[10px] py-6 h-155 gap-6">
            <div class="flex flex-col justify-center text-center gap-5">
                <x-cover src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nickname) }}" alt="User Profile Image" class="mask mask-circle h-[13vh]"></x-cover>
                <h1 class="font-bold text-[1.3rem] text-secondary">{{ auth()->user()->nickname }}</h1>
                <h2 class="font-bold">Bio</h2>
                <p class="break-all px-7">{{ auth()->user()->bio }}</p>
                <div class="flex flex-row justify-center gap-3 mt-2">
                    <button class="btn">FOLLOW</button>
                    <button class="btn">MESSAGE</button>
                </div>
            </div>
            <ul class="flex flex-col gap-4 w-full p-0 mt-3">
                <x-nav-link-edit x-on:click="activeMenu = 'menu1'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu1', 'border-transparent': activeMenu !== 'menu1' }">Profile</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu2'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu2', 'border-transparent': activeMenu !== 'menu2'}">My Reviews</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu3'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu3', 'border-transparent': activeMenu !== 'menu3' }">My Comments</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu4'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu4', 'border-transparent': activeMenu !== 'menu4' }">Settings</x-nav-link-edit>
            </ul>
        </section>
        <section class="flex flex-col gap-5 w-full border border-[#88888833] bg-[#0E0E0E] rounded-[10px] p-6">

            <!-- User Edit Form -->
            <x-form.form method="POST" action="{{ route('users.edit', auth()->user()->id) }}" class="w-full" x-show="activeMenu === 'menu1'">
                @csrf
                @method('PUT')
                <x-form.field>
                    <x-form.description title="Profile"
                        description="Manage your public profile and account information."></x-form.description>
                </x-form.field>
                <x-divisor class="mb-5"></x-divisor>
                <x-form.field>
                    <x-form.label for="name">Name</x-form.label>
                    <x-form.input type="text" name="name" id="name" placeholder="Your Name" maxlength="100" value="{{ auth()->user()->name }}"/>
                </x-form.field>
                <x-form.field>
                    <x-form.label for="nickname">Nickname</x-form.label>
                    <x-form.input type="text" name="nickname" id="nickname" placeholder="This nickname will be displayed publicly on the site"
                        maxlength="35" value="{{ auth()->user()->nickname }}"/>
                </x-form.field>
                <x-form.field>
                    <x-form.label for="email">Email</x-form.label>
                    <x-form.input type="email" name="email" id="email" placeholder="you@example.com" maxlength="255" value="{{ auth()->user()->email }}"/>
                </x-form.field>
                <div class="flex flex-row justify-between gap-3">
                    <x-form.field class="flex-1/2">
                        <x-form.label for="password">Password</x-form.label>
                        <x-form.input type="password" name="password" id="password" placeholder="******" />
                    </x-form.field>
                    <x-form.field class="flex-1/2">
                        <x-form.label for="password_confirmation">Confirm Password</x-form.label>
                        <x-form.input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="******" minlenght="8"/>
                    </x-form.field>
                </div>
                <x-form.field>
                    <x-form.label for="avatar">Profile Picture (optional)</x-form.label>
                    <x-form.input type="file" name="avatar" id="avatar" />
                </x-form.field>
                <x-form.field>
                    <x-form.label for="bio">Bio (optional)</x-form.label>
                    <x-form.input type="textarea" name="bio" id="bio" placeholder="Tell us about yourself" value="{{ auth()->user()->bio }}" maxlength="160" />
                </x-form.field>
                <x-form.field class="mt-2 flex items-end">
                    <x-form.button class="w-40">Update</x-form.button>
                </x-form.field>
            </x-form.form>

            <!-- My Reviews -->
            <div id="review" x-show="activeMenu === 'menu2'">
                <h2 class="font-bold text-[1.7rem] mb-2">My Reviews ({{ $reviews->total() }})</h2>
                <p>Manage your reviews.</p>
                <x-divisor class="my-5"></x-divisor>
                @if(!empty($reviews))
                    <div class="flex flex-col gap-6">
                        @foreach($reviews as $review)
                            <x-article-link :review="$review" :dashboard="true"></x-article-link>
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $reviews->links() }}
                    </div>
                @else
                    <p class="font-bold">Nenhuma review encontrada.</p>
                @endif
            </div>

            <!-- My Comments -->
            <div id="comment" x-show="activeMenu === 'menu3'">
                <h2 class="font-bold text-[1.7rem] mb-2">My Comments ({{ $comments->total() }})</h2>
                <p>Manage your comments.</p>
                <x-divisor class="my-5"></x-divisor>
                @if(!empty($comments))
                    <ul id="comments" class="flex flex-col gap-6 mt-5">
                        @foreach ($comments as $comment)
                            <x-comment :comment="$comment" :dashboard="true"></x-comment>
                        @endforeach
                    </ul>
                    <div class="mt-10">
                        {{ $comments->links() }}
                    </div>
                @else
                    <p class="font-bold">Nenhum comentário encontrado</p>
                @endif
            </div>

            <!-- Settings -->
        </section>
    </div>
</x-layout>