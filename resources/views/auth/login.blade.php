<x-layout>
    <x-slot:title>{{ __('auth_ui.login_title') }}</x-slot:title>
    <x-form.form method="POST" action="/login" class="mx-auto my-[10vh] w-[calc(100%-2rem)] max-w-md border border-[#88888833] bg-base-300 rounded-[20px] p-6 sm:p-8 sm:my-[15vh] shadow-xl shadow-black/30">
        @csrf
        <x-form.field class="text-center mb-6">
            <x-form.description :title="__('auth_ui.login_heading')" :description="__('auth_ui.login_description')"></x-form.description>
        </x-form.field>
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="email">{{ __('auth_ui.email') }}</x-form.label>
            <x-form.input type="email" name="email" id="email" :placeholder="__('auth_ui.email_placeholder')" required/>
        </x-form.field>
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="password">{{ __('auth_ui.password') }}</x-form.label>
            <x-form.input type="password" name="password" id="password" :placeholder="__('auth_ui.password_placeholder')" minlenght="8" required/>
        </x-form.field>
        <x-form.field class="mt-3">
            <x-form.button class="shadow-lg shadow-primary/30 hover:shadow-primary/50 transition-shadow">{{ __('auth_ui.sign_in') }}</x-form.button>
        </x-form.field>
        <p class="mt-3 text-center text-sm">
            <a href="{{ route('password.request') }}" class="text-primary font-bold hover:underline">{{ __('auth_ui.forgot_password') }}</a>
        </p>
        <form-field>
            <p class="text-center text-sm text-base-content/50 mt-4">
                {{ __('auth_ui.no_account') }}
                <a href="/register" class="text-primary font-bold hover:underline">{{ __('auth_ui.sign_up') }}</a>
            </p>
        </form-field>
    </x-form.form>
</x-layout>
