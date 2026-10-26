@php
    $avatar = !empty(auth()->user()->avatar)
        ? auth()->user()->avatar_url
        : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->nickname);
@endphp

<x-layout>
    <x-slot:title>{{ __('dashboard.title', ['nickname' => auth()->user()->nickname]) }}</x-slot:title>
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 lg:py-10 mb-12 sm:mb-16 flex min-w-0 flex-col gap-6 lg:gap-8 xl:flex-row" x-data="{ activeMenu: @js($menu) }">
        <aside class="flex w-full min-w-0 flex-col gap-6 rounded-2xl border border-[#88888833] bg-base-300 p-6 xl:w-80 shrink-0 self-start">
            <div class="flex flex-col items-center text-center gap-3">
                <div class="avatar">
                    <div class="w-24 sm:w-28 rounded-full ring-2 ring-primary/40 ring-offset-2 ring-offset-[#0E0E0E] overflow-hidden">
                        <img src="{{ $avatar }}" alt="{{ __('dashboard.avatar_alt') }}" class="w-full h-full object-cover">
                    </div>
                </div>
                <div class="mt-1">
                    <h1 class="font-bold text-xl text-secondary">{{ auth()->user()->nickname }}</h1>
                    <p class="text-xs text-base-content/60 font-medium">{{ auth()->user()->name }}</p>
                </div>
                @if(filled(auth()->user()->bio))
                    <div class="mt-1 w-full">
                        <h2 class="text-xs uppercase tracking-wider font-semibold text-base-content/50 mb-1">{{ __('dashboard.bio') }}</h2>
                        <p class="text-sm text-base-content/80 break-words px-2 leading-relaxed">{{ auth()->user()->bio }}</p>
                    </div>
                @endif
                <div class="flex flex-row flex-wrap justify-center gap-2 mt-2 w-full">
                    <button type="button" disabled aria-disabled="true" class="btn btn-sm btn-outline border-white/20 hover:border-accent hover:text-accent font-bold flex-1 min-h-[40px] opacity-60 text-xs">{{ __('dashboard.follow') }}</button>
                    <button type="button" disabled aria-disabled="true" class="btn btn-sm btn-outline border-white/20 hover:border-accent hover:text-accent font-bold flex-1 min-h-[40px] opacity-60 text-xs">{{ __('dashboard.message') }}</button>
                </div>
            </div>
            <x-divisor class="my-1 opacity-50"></x-divisor>
            <nav aria-label="{{ __('dashboard.title', ['nickname' => auth()->user()->nickname]) }}">
                <ul role="tablist" class="grid w-full grid-cols-2 gap-2 p-0 xl:flex xl:flex-col xl:gap-2">
                    <x-nav-link-edit targetMenu="menu1" x-on:click="activeMenu = 'menu1'" x-bind:class="{ 'border-accent text-accent bg-white/5': activeMenu === 'menu1', 'border-transparent text-base-content/70 hover:text-base-content hover:bg-white/5': activeMenu !== 'menu1' }">
                        <span class="flex items-center gap-2.5">
                            <x-heroicon-o-user class="size-4 shrink-0" />
                            <span>{{ __('dashboard.profile') }}</span>
                        </span>
                    </x-nav-link-edit>
                    <x-nav-link-edit targetMenu="menu2" x-on:click="activeMenu = 'menu2'" x-bind:class="{ 'border-accent text-accent bg-white/5': activeMenu === 'menu2', 'border-transparent text-base-content/70 hover:text-base-content hover:bg-white/5': activeMenu !== 'menu2' }">
                        <span class="flex items-center gap-2.5">
                            <x-heroicon-o-document-text class="size-4 shrink-0" />
                            <span>{{ __('dashboard.my_reviews') }}</span>
                        </span>
                    </x-nav-link-edit>
                    <x-nav-link-edit targetMenu="menu3" x-on:click="activeMenu = 'menu3'" x-bind:class="{ 'border-accent text-accent bg-white/5': activeMenu === 'menu3', 'border-transparent text-base-content/70 hover:text-base-content hover:bg-white/5': activeMenu !== 'menu3' }">
                        <span class="flex items-center gap-2.5">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="size-4 shrink-0" />
                            <span>{{ __('dashboard.my_comments') }}</span>
                        </span>
                    </x-nav-link-edit>
                    <x-nav-link-edit targetMenu="menu4" x-on:click="activeMenu = 'menu4'" x-bind:class="{ 'border-accent text-accent bg-white/5': activeMenu === 'menu4', 'border-transparent text-base-content/70 hover:text-base-content hover:bg-white/5': activeMenu !== 'menu4' }">
                        <span class="flex items-center gap-2.5">
                            <x-heroicon-o-cog-6-tooth class="size-4 shrink-0" />
                            <span>{{ __('dashboard.settings') }}</span>
                        </span>
                    </x-nav-link-edit>
                </ul>
            </nav>
        </aside>

        <section class="flex flex-1 w-full min-w-0 flex-col rounded-2xl border border-[#88888833] bg-base-300 p-4 sm:p-6 lg:p-8">
            <!-- User Edit Form -->
            <div x-show="activeMenu === 'menu1'" role="tabpanel" id="panel-menu1" aria-labelledby="tab-menu1">
                <x-form.form method="POST" action="/users/{{ auth()->user()->id }}" class="w-full flex flex-col gap-5" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <x-form.field>
                        <x-form.description :title="__('dashboard.profile')"
                            :description="__('dashboard.manage_profile')"></x-form.description>
                    </x-form.field>
                    <x-divisor class="mb-2"></x-divisor>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.field>
                            <x-form.label for="name">{{ __('dashboard.name') }}</x-form.label>
                            <x-form.input type="text" name="name" id="name" :placeholder="__('dashboard.name_placeholder')" maxlength="100" value="{{ auth()->user()->name }}"/>
                        </x-form.field>
                        <x-form.field>
                            <x-form.label for="nickname">{{ __('dashboard.nickname') }}</x-form.label>
                            <x-form.input type="text" name="nickname" id="nickname" :placeholder="__('dashboard.nickname_placeholder')"
                                maxlength="35" value="{{ auth()->user()->nickname }}"/>
                        </x-form.field>
                    </div>
                    <x-form.field>
                        <x-form.label for="email">{{ __('dashboard.email') }}</x-form.label>
                        <x-form.input type="email" name="email" id="email" :placeholder="__('dashboard.email_placeholder')" maxlength="255" value="{{ auth()->user()->email }}"/>
                    </x-form.field>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.field>
                            <x-form.label for="password">{{ __('dashboard.password') }}</x-form.label>
                            <x-form.input type="password" name="password" id="password" :placeholder="__('dashboard.password_placeholder')" minlength="8" />
                            <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.password_helper') }}</p>
                        </x-form.field>
                        <x-form.field>
                            <x-form.label for="password_confirmation">{{ __('dashboard.confirm_password') }}</x-form.label>
                            <x-form.input type="password" name="password_confirmation" id="password_confirmation"
                                :placeholder="__('dashboard.password_placeholder')" minlength="8" />
                        </x-form.field>
                    </div>
                    <x-form.field>
                        <x-form.label for="avatar">{{ __('dashboard.profile_picture_optional') }}</x-form.label>
                        <div class="flex items-center gap-3">
                            <div class="avatar shrink-0">
                                <div class="w-11 h-11 rounded-full overflow-hidden ring-1 ring-white/20">
                                    <img src="{{ $avatar }}" alt="{{ __('dashboard.avatar_alt') }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <input type="file" name="avatar" id="avatar" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif" class="file-input file-input-bordered file-input-sm sm:file-input-md w-full rounded-2xl bg-base-300 border-[#8888884A] text-sm focus:border-accent focus:outline-none" />
                            </div>
                        </div>
                        <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.avatar_helper') }}</p>
                        <x-form.error name="avatar" />
                    </x-form.field>
                    <x-form.field>
                        <x-form.label for="bio">{{ __('dashboard.bio_optional') }}</x-form.label>
                        <x-form.input type="textarea" name="bio" id="bio" rows="3" :placeholder="__('dashboard.bio_placeholder')" value="{{ auth()->user()->bio }}" maxlength="160" />
                    </x-form.field>
                    <x-form.field class="mt-4 flex flex-col sm:flex-row sm:justify-end">
                        <x-form.button class="w-full sm:w-auto min-h-[44px] px-8 text-[.90rem]">{{ __('dashboard.update') }}</x-form.button>
                    </x-form.field>
                </x-form.form>
            </div>

            <!-- My Reviews -->
            <div x-show="activeMenu === 'menu2'" role="tabpanel" id="panel-menu2" aria-labelledby="tab-menu2">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
                    <div>
                        <h2 class="font-bold text-2xl sm:text-[1.7rem]">{{ trans_choice('dashboard.reviews_count', $reviews->total(), ['count' => $reviews->total()]) }}</h2>
                        <p class="text-sm text-base-content/70 mt-1">{{ __('dashboard.manage_reviews') }}</p>
                    </div>
                    <a href="{{ route('reviews.create') }}" class="btn btn-sm sm:btn-md btn-outline border-white/20 hover:border-accent hover:text-accent font-bold gap-2 min-h-[44px] px-4 self-start sm:self-auto">
                        <x-heroicon-s-plus class="size-4" />
                        <span>{{ __('dashboard.new_review') }}</span>
                    </a>
                </div>
                <x-divisor class="my-6"></x-divisor>
                @if($reviews->isNotEmpty())
                    <div class="flex flex-col gap-4 sm:gap-5">
                        @foreach($reviews as $review)
                            <x-article-link :review="$review" :dashboard="true"></x-article-link>
                        @endforeach
                    </div>
                    <x-pagination :pagination="$reviews" class="mt-8"></x-pagination>
                @else
                    <div class="py-12 flex flex-col items-center justify-center text-center gap-4">
                        <x-heroicon-s-document-text class="text-accent size-20 opacity-80" />
                        <h3 class="font-bold text-xl sm:text-2xl">{{ __('dashboard.no_reviews') }}</h3>
                        <p class="text-sm text-base-content/70 max-w-md">{{ __('dashboard.first_review_prompt') }}</p>
                        <a href="{{ route('reviews.create') }}" class="btn btn-primary font-bold min-h-[44px] mt-2 gap-2">
                            <x-heroicon-s-plus class="size-4" />
                            <span>{{ __('dashboard.new_review') }}</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- My Comments -->
            <div x-show="activeMenu === 'menu3'" role="tabpanel" id="panel-menu3" aria-labelledby="tab-menu3">
                <div class="mb-2">
                    <h2 class="font-bold text-2xl sm:text-[1.7rem]">{{ trans_choice('dashboard.comments_count', $comments->total(), ['count' => $comments->total()]) }}</h2>
                    <p class="text-sm text-base-content/70 mt-1">{{ __('dashboard.manage_comments') }}</p>
                </div>
                <x-divisor class="my-6"></x-divisor>
                @if($comments->isNotEmpty())
                    <ul id="comments-list" class="flex flex-col gap-4">
                        @foreach ($comments as $comment)
                            <x-comment :comment="$comment" :dashboard="true"></x-comment>
                        @endforeach
                    </ul>
                    <x-pagination :pagination="$comments" class="mt-8"></x-pagination>
                @else
                    <div class="py-12 flex flex-col items-center justify-center text-center gap-4">
                        <x-heroicon-s-chat-bubble-oval-left-ellipsis class="text-accent size-20 opacity-80" />
                        <h3 class="font-bold text-xl sm:text-2xl">{{ __('dashboard.no_comments') }}</h3>
                        <p class="text-sm text-base-content/70 max-w-md">{{ __('dashboard.no_comments_description') }}</p>
                        <a href="/" class="btn btn-outline border-white/20 hover:border-accent hover:text-accent font-bold min-h-[44px] mt-2 gap-2">
                            {{ __('reviews.back_to_reviews') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Settings -->
            <div x-show="activeMenu === 'menu4'" role="tabpanel" id="panel-menu4" aria-labelledby="tab-menu4">
                <div class="mb-2">
                    <h2 class="font-bold text-2xl sm:text-[1.7rem]">{{ __('dashboard.settings') }}</h2>
                    <p class="text-sm text-base-content/70 mt-1">{{ __('dashboard.settings_description') }}</p>
                </div>
                <x-divisor class="my-6"></x-divisor>

                <x-form.form method="POST" action="/users/{{ auth()->user()->id }}" class="w-full flex flex-col gap-6">
                    @csrf
                    @method('PUT')

                    <!-- Security -->
                    <div class="rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <x-heroicon-o-shield-check class="size-5 text-accent" />
                            <span>{{ __('dashboard.security') }}</span>
                        </h3>
                        <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.security_description') }}</p>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('password.request') }}" class="btn btn-outline border-white/20 hover:border-accent hover:text-accent font-bold min-h-[44px] w-full sm:w-auto gap-2">
                                    <x-heroicon-o-key class="size-4" />
                                    <span>{{ __('dashboard.reset_password') }}</span>
                                </a>
                                <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.reset_password_description') }}</p>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('verification.send') }}" class="btn btn-outline border-white/20 hover:border-accent hover:text-accent font-bold min-h-[44px] w-full sm:w-auto gap-2">
                                    <x-heroicon-o-envelope class="size-4" />
                                    <span>{{ __('dashboard.resend_verification') }}</span>
                                </a>
                                <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.resend_verification_description') }}</p>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-white/10">
                            <p class="text-sm font-medium text-base-content/80">{{ __('dashboard.verification_status') }}</p>
                            <span class="inline-flex items-center gap-1.5 mt-1 px-2 py-1 rounded text-xs font-semibold 
                                {{ auth()->user()->hasVerifiedEmail() ? 'bg-green/10 text-green' : 'bg-amber/10 text-amber' }}">
                                <x-heroicon-o-{{ auth()->user()->hasVerifiedEmail() ? 'check-circle' : 'exclamation-circle' }} class="size-3.5" />
                                {{ auth()->user()->hasVerifiedEmail() ? __('dashboard.verified') : __('dashboard.not_verified') }}
                            </span>
                        </div>
                    </div>

                    <!-- Display Preferences -->
                    <div class="rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <x-heroicon-o-eye class="size-5 text-accent" />
                            <span>{{ __('dashboard.display_preferences') }}</span>
                        </h3>
                        <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.display_preferences_description') }}</p>
                        <x-form.toggle-field
                            name="hide_spoilers_default"
                            :title="__('dashboard.hide_spoilers_default')"
                            :description="__('dashboard.hide_spoilers_default_description')"
                            :checked="auth()->user()->hide_spoilers_default"
                        />
                        <x-form.toggle-field
                            name="show_views_count"
                            :title="__('dashboard.show_views_count')"
                            :description="__('dashboard.show_views_count_description')"
                            :checked="auth()->user()->show_views_count"
                        />
                    </div>

                    <!-- Privacy -->
                    <div class="rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <x-heroicon-o-lock-closed class="size-5 text-accent" />
                            <span>{{ __('dashboard.privacy') }}</span>
                        </h3>
                        <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.privacy_description') }}</p>
                        <x-form.toggle-field
                            name="private_profile"
                            :title="__('dashboard.private_profile')"
                            :description="__('dashboard.private_profile_description')"
                            :checked="auth()->user()->private_profile"
                        />
                        <x-form.toggle-field
                            name="hide_email"
                            :title="__('dashboard.hide_email')"
                            :description="__('dashboard.hide_email_description')"
                            :checked="auth()->user()->hide_email"
                        />
                    </div>

                    <!-- Theme -->
                    <div class="rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                        <h3 class="font-bold text-lg flex items-center gap-2">
                            <x-heroicon-o-sun class="size-5 text-accent" />
                            <span>{{ __('dashboard.theme') }}</span>
                        </h3>
                        <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.theme_description') }}</p>
                        <select id="theme-select" class="select select-bordered select-sm min-h-[44px] w-full sm:w-auto bg-base-300 border-base-content/20">
                            <option value="system" @selected(auth()->user()->theme_preference === 'system')>{{ __('dashboard.theme_system') }}</option>
                            <option value="light" @selected(auth()->user()->theme_preference === 'light')>{{ __('dashboard.theme_light') }}</option>
                            <option value="dark" @selected(auth()->user()->theme_preference === 'dark')>{{ __('dashboard.theme_dark') }}</option>
                        </select>
                    </div>

                    <x-form.field class="mt-4 flex flex-col sm:flex-row sm:justify-end">
                        <x-form.button class="w-full sm:w-auto min-h-[44px] px-8 text-[.90rem]">{{ __('dashboard.save_settings') }}</x-form.button>
                    </x-form.field>
                </x-form.form>

                <div class="mt-6 rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <x-heroicon-o-language class="size-5 text-accent" />
                        <span>{{ __('dashboard.language_preference') }}</span>
                    </h3>
                    <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.language_preference_description') }}</p>
                    <form method="POST" action="{{ route('locale.update') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mt-2">
                        @csrf
                        <label for="dashboard-locale-picker" class="sr-only">{{ __('locale.label') }}</label>
                        <select class="select select-bordered select-sm min-h-[44px] w-full sm:w-auto font-bold bg-base-300" name="locale" id="dashboard-locale-picker" onchange="this.form.submit()">
                            <option value="pt_BR" @selected(app()->getLocale() === 'pt_BR')>Português (BR)</option>
                            <option value="en" @selected(app()->getLocale() === 'en')>English (US)</option>
                        </select>
                    </form>
                </div>

                <!-- Data -->
                <div class="mt-6 rounded-xl border border-white/10 bg-base-200 p-5 sm:p-6 flex flex-col gap-4">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <x-heroicon-o-server-stack class="size-5 text-accent" />
                        <span>{{ __('dashboard.data') }}</span>
                    </h3>
                    <p class="text-sm text-base-content/70 leading-relaxed">{{ __('dashboard.data_description') }}</p>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('users.export', auth()->user()) }}" class="btn btn-outline border-white/20 hover:border-accent hover:text-accent font-bold min-h-[44px] w-full sm:w-auto gap-2">
                                <x-heroicon-o-arrow-down-tray class="size-4" />
                                <span>{{ __('dashboard.export_data') }}</span>
                            </a>
                            <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.export_data_description') }}</p>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-white/10">
                        <button onclick="deleteModal.showModal()" class="btn btn-error hover:bg-red-600 font-bold min-h-[44px] w-full sm:w-auto gap-2">
                            <x-heroicon-o-trash class="size-4" />
                            <span>{{ __('dashboard.delete_account') }}</span>
                        </button>
                        <p class="text-xs text-base-content/50 mt-1">{{ __('dashboard.delete_account_description') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Delete Account Modal -->
    <dialog id="deleteModal" class="modal">
        <div class="modal-box bg-base-200 border border-white/10">
            <h3 class="font-bold text-lg mb-2">{{ __('dashboard.delete_account') }}</h3>
            <p class="text-sm text-base-content/70 mb-6">{{ __('dashboard.delete_account_confirm') }}</p>
            <div class="flex gap-3 justify-end">
                <form method="dialog">
                    <button class="btn btn-outline border-white/20 hover:bg-base-300">{{ __('dashboard.cancel') }}</button>
                </form>
                <form method="POST" action="{{ url('/users/'.auth()->user()->getRouteKey()) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-error hover:bg-red-600">{{ __('dashboard.delete_account_button') }}</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</x-layout>
