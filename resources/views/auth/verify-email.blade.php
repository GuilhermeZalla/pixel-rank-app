<x-layout>
    <x-slot:title>{{ __('auth_ui.verify_email_title') }}</x-slot:title>
    <main class="mx-auto my-[12vh] max-w-xl rounded-[20px] border border-[#88888833] bg-[#0E0E0E] p-6 text-center">
        <h1 class="mb-3 text-2xl font-bold">{{ __('auth_ui.verify_email_heading') }}</h1>
        <p class="mb-5 text-base-content/70">{{ __('auth_ui.verify_email_description', ['email' => auth()->user()->email]) }}</p>
        @if (session('status') === 'verification-link-sent')
            <p class="mb-4 text-success" role="status">{{ __('auth_ui.verification_link_sent') }}</p>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary" type="submit">{{ __('auth_ui.resend_verification_email') }}</button>
        </form>
        <form method="POST" action="/logout" class="mt-4">
            @csrf
            @method('DELETE')
            <button class="btn btn-ghost" type="submit">{{ __('auth_ui.sign_out') }}</button>
        </form>
    </main>
</x-layout>
