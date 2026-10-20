@php
    $avatar = !empty(auth()->user()->avatar)
        ? asset(auth()->user()->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->nickname);
@endphp

<x-layout>
    <x-slot:title>{{ __('dashboard.title', ['nickname' => auth()->user()->nickname]) }}</x-slot:title>
    <div class="mx-4 mt-[5vh] flex min-w-0 flex-col justify-between gap-6 lg:mx-[2vw] xl:flex-row xl:gap-8" x-data="{ activeMenu: @js($menu) }">
        <section class="flex h-auto w-full min-w-0 flex-col gap-6 rounded-[10px] border border-[#88888833] bg-[#0E0E0E] py-6 xl:h-155 xl:w-100">
            <div class="flex flex-col justify-center text-center gap-5">
                <x-cover src="{{ $avatar }}" :alt="__('dashboard.avatar_alt')" class="mask mask-circle object-contain h-[13vh]"></x-cover>
                <h1 class="font-bold text-[1.3rem] text-secondary">{{ auth()->user()->nickname }}</h1>
                <h2 class="font-bold">{{ __('dashboard.bio') }}</h2>
                <p class="break-all px-7">{{ auth()->user()->bio }}</p>
                <div class="flex flex-row flex-wrap justify-center gap-3 mt-2">
                    <button class="btn">{{ __('dashboard.follow') }}</button>
                    <button class="btn">{{ __('dashboard.message') }}</button>
                </div>
            </div>
            <ul class="mt-3 grid w-full grid-cols-2 gap-3 p-0 xl:flex xl:flex-col xl:gap-4">
                <x-nav-link-edit x-on:click="activeMenu = 'menu1'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu1', 'border-transparent': activeMenu !== 'menu1' }">{{ __('dashboard.profile') }}</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu2'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu2', 'border-transparent': activeMenu !== 'menu2'}">{{ __('dashboard.my_reviews') }}</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu3'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu3', 'border-transparent': activeMenu !== 'menu3' }">{{ __('dashboard.my_comments') }}</x-nav-link-edit>
                <x-nav-link-edit x-on:click="activeMenu = 'menu4'" x-bind:class="{ 'border-accent text-accent' : activeMenu === 'menu4', 'border-transparent': activeMenu !== 'menu4' }">{{ __('dashboard.settings') }}</x-nav-link-edit>
            </ul>
        </section>
        <section class="flex w-full min-w-0 flex-col gap-5 rounded-[10px] border border-[#88888833] bg-[#0E0E0E] p-4 sm:p-6">

            <!-- User Edit Form -->
            <x-form.form method="POST" action="/users/{{ auth()->user()->id }}" class="w-full" x-show="activeMenu === 'menu1'" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <x-form.field>
                    <x-form.description :title="__('dashboard.profile')"
                        :description="__('dashboard.manage_profile')"></x-form.description>
                </x-form.field>
                <x-divisor class="mb-5"></x-divisor>
                <x-form.field>
                    <x-form.label for="name">{{ __('dashboard.name') }}</x-form.label>
                    <x-form.input type="text" name="name" id="name" :placeholder="__('dashboard.name_placeholder')" maxlength="100" value="{{ auth()->user()->name }}"/>
                </x-form.field>
                <x-form.field>
                    <x-form.label for="nickname">{{ __('dashboard.nickname') }}</x-form.label>
                    <x-form.input type="text" name="nickname" id="nickname" :placeholder="__('dashboard.nickname_placeholder')"
                        maxlength="35" value="{{ auth()->user()->nickname }}"/>
                </x-form.field>
                <x-form.field>
                    <x-form.label for="email">{{ __('dashboard.email') }}</x-form.label>
                    <x-form.input type="email" name="email" id="email" :placeholder="__('dashboard.email_placeholder')" maxlength="255" value="{{ auth()->user()->email }}"/>
                </x-form.field>
                <div class="flex flex-col justify-between gap-3 sm:flex-row">
                    <x-form.field class="flex-1/2">
                        <x-form.label for="password">{{ __('dashboard.password') }}</x-form.label>
                        <x-form.input type="password" name="password" id="password" :placeholder="__('dashboard.password_placeholder')" minlength="8" />
                    </x-form.field>
                    <x-form.field class="flex-1/2">
                        <x-form.label for="password_confirmation">{{ __('dashboard.confirm_password') }}</x-form.label>
                        <x-form.input type="password" name="password_confirmation" id="password_confirmation"
                            :placeholder="__('dashboard.password_placeholder')" minlength="8" />
                    </x-form.field>
                </div>
                <x-form.field>
                    <x-form.label for="avatar">{{ __('dashboard.profile_picture_optional') }}</x-form.label>
                    <x-form.input type="file" name="avatar" id="avatar" />
                </x-form.field>
                <x-form.field>
                    <x-form.label for="bio">{{ __('dashboard.bio_optional') }}</x-form.label>
                    <x-form.input type="textarea" name="bio" id="bio" :placeholder="__('dashboard.bio_placeholder')" value="{{ auth()->user()->bio }}" maxlength="160" />
                </x-form.field>
                <x-form.field class="mt-2 flex items-end">
                    <x-form.button class="w-40">{{ __('dashboard.update') }}</x-form.button>
                </x-form.field>
            </x-form.form>

            <!-- My Reviews -->
            <div id="review" x-show="activeMenu === 'menu2'">
                <h2 class="mb-2 flex flex-col justify-between gap-3 font-bold text-[1.7rem] sm:flex-row sm:items-center">{{ trans_choice('dashboard.reviews_count', $reviews->total(), ['count' => $reviews->total()]) }} <x-nav-link href="{{ route('reviews.create') }}" class="flex flex-row gap-2 items-center text-[.75rem] border-2 hover:border-accent hover:text-accent py-4 badge border-[#8888884A] font-bold"><x-heroicon-s-plus class="size-4" />{{ __('dashboard.new_review') }}</x-nav-link></h2>
                <p>{{ __('dashboard.manage_reviews') }}</p>
                <x-divisor class="my-7"></x-divisor>
                @if(count($reviews) !== 0)
                    <div class="flex flex-col gap-6">
                        @foreach($reviews as $review)
                            <x-article-link :review="$review" :dashboard="true"></x-article-link>
                        @endforeach
                    </div>
                    <x-pagination :pagination="$reviews" class="mt-10"></x-pagination>
                @else
                    <x-empty-result type="reviews" :title="__('dashboard.no_reviews')"></x-empty-result>
                @endif
            </div>

            <!-- My Comments -->
            <div id="comment" x-show="activeMenu === 'menu3'">
                <h2 class="font-bold text-[1.7rem] mb-2">{{ trans_choice('dashboard.comments_count', $comments->total(), ['count' => $comments->total()]) }}</h2>
                <p>{{ __('dashboard.manage_comments') }}</p>
                <x-divisor class="my-5"></x-divisor>
                @if(count($comments) !== 0)
                    <ul id="comments" class="flex flex-col gap-6 mt-5">
                        @foreach ($comments as $comment)
                            <x-comment :comment="$comment" :dashboard="true"></x-comment>
                        @endforeach
                    </ul>
                     <x-pagination :pagination="$comments" class="mt-10"></x-pagination>
                @else
                    <x-empty-result type="comments" :title="__('dashboard.no_comments')"></x-empty-result>
                @endif
            </div>

            <!-- Settings -->
        </section>
    </div>
</x-layout>
