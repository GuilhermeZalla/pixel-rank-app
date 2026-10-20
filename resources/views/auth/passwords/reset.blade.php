<x-layout>
    <x-slot:title>{{ __('auth_ui.reset_form_title') }}</x-slot:title>
    <x-form.form method="POST" action="{{ route('password.update') }}" class="mx-auto my-[10vh] w-[calc(100%-2rem)] max-w-md border border-[#88888833] bg-[#0E0E0E] rounded-[20px] p-6 sm:my-[15vh]">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <x-form.field class="text-center mb-6">
            <x-form.description :title="__('auth_ui.reset_form_title')" :description="__('auth_ui.reset_form_description')"></x-form.description>
        </x-form.field>
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="email">{{ __('auth_ui.email') }}</x-form.label>
            <x-form.input type="email" name="email" id="email" value="{{ old('email', $email) }}" required autocomplete="email" />
            @error('email')<span class="text-error">{{ $message }}</span>@enderror
        </x-form.field>
        <x-form.field class="mt-3 flex flex-col gap-2">
            <x-form.label for="password">{{ __('auth_ui.new_password') }}</x-form.label>
            <x-form.input type="password" name="password" id="password" required minlength="8" autocomplete="new-password" />
            @error('password')<span class="text-error">{{ $message }}</span>@enderror
        </x-form.field>
        <x-form.field class="mt-3 flex flex-col gap-2">
            <x-form.label for="password_confirmation">{{ __('auth_ui.confirm_new_password') }}</x-form.label>
            <x-form.input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password" />
        </x-form.field>
        <x-form.field class="mt-4"><x-form.button>{{ __('auth_ui.reset_password') }}</x-form.button></x-form.field>
    </x-form.form>
</x-layout>
