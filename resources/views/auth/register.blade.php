<x-layout>
    <x-slot:title>{{ __('auth_ui.register_title') }}</x-slot:title>
    <x-form.form method="POST" action="/register" enctype="multipart/form-data" class="mx-auto my-[6vh] w-[calc(100%-2rem)] max-w-lg border border-[#88888833] bg-base-300 rounded-[20px] p-4 sm:p-6 sm:p-8 shadow-xl shadow-black/30">
        @csrf
        <x-form.field class="text-center mb-6">
            <x-form.description :title="__('auth_ui.create_account')"
                :description="__('auth_ui.register_description')"></x-form.description>
        </x-form.field>
        <x-form.field>
            <x-form.label for="name">{{ __('auth_ui.name') }}</x-form.label>
            <x-form.input type="text" name="name" id="name" :placeholder="__('auth_ui.name_placeholder')" required maxlength="100"/>
        </x-form.field>
         <x-form.field>
            <x-form.label for="nickname">{{ __('auth_ui.nickname') }}</x-form.label>
            <x-form.input type="text" name="nickname" id="nickname" :placeholder="__('auth_ui.nickname_placeholder')" required minlength="5" maxlength="35"/>
        </x-form.field>
        <x-form.field>
            <x-form.label for="email">{{ __('auth_ui.email') }}</x-form.label>
            <x-form.input type="email" name="email" id="email" :placeholder="__('auth_ui.email_placeholder')" required maxlength="255"/>
        </x-form.field>
        <div class="flex flex-col justify-between gap-3 sm:flex-row">
            <x-form.field class="flex-1/2">
                <x-form.label for="password">{{ __('auth_ui.password') }}</x-form.label>
                <x-form.input type="password" name="password" id="password" :placeholder="__('auth_ui.password_placeholder')" />
            </x-form.field>
            <x-form.field class="flex-1/2">
                <x-form.label for="password_confirmation">{{ __('auth_ui.confirm_password') }}</x-form.label>
                <x-form.input type="password" name="password_confirmation" id="password_confirmation" :placeholder="__('auth_ui.password_placeholder')"
                    required minlenght="8" />
            </x-form.field>
        </div>
        <x-form.field>
            <x-form.label for="avatar">{{ __('auth_ui.profile_picture_optional') }}</x-form.label>
            <x-form.input type="file" name="avatar" id="avatar" accept="image/*" />
        </x-form.field>
        <x-form.field>
            <x-form.label for="bio">{{ __('auth_ui.bio_optional') }}</x-form.label>
            <x-form.input type="textarea" name="bio" id="bio" :placeholder="__('auth_ui.bio_placeholder')" maxlength="160" />
        </x-form.field>
        <x-form.field class="mt-2">
            <x-form.button class="shadow-lg shadow-primary/30 hover:shadow-primary/50 transition-shadow">{{ __('auth_ui.create_account') }}</x-form.button>
        </x-form.field>
    </x-form.form>
</x-layout>
