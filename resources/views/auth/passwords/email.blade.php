<x-layout>
    <x-slot:title>{{ __('auth_ui.forgot_password_title') }}</x-slot:title>
    <x-form.form method="POST" action="{{ route('password.email') }}" class="mx-auto my-[10vh] w-[calc(100%-2rem)] max-w-md border border-[#88888833] bg-base-300 rounded-[20px] p-6 sm:my-[15vh]">
        @csrf
        <x-form.field class="text-center mb-6">
            <x-form.description :title="__('auth_ui.reset_password_heading')" :description="__('auth_ui.reset_password_description')"></x-form.description>
        </x-form.field>
        @if (session('status'))<p class="mb-4 text-success" role="status">{{ session('status') }}</p>@endif
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="email">{{ __('auth_ui.email') }}</x-form.label>
            <x-form.input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email" />
            @error('email')<span class="text-error">{{ $message }}</span>@enderror
        </x-form.field>
        <x-form.field class="mt-4"><x-form.button>{{ __('auth_ui.send_reset_link') }}</x-form.button></x-form.field>
        <p class="mt-4 text-center text-sm"><a class="link" href="{{ route('login') }}">{{ __('auth_ui.back_to_sign_in') }}</a></p>
    </x-form.form>
</x-layout>
