<x-layout>
    <x-slot:title>Login</x-slot:title>
    <x-form.form method="POST" action="/login" class="m-[15vh] border border-[#88888833] bg-[#0E0E0E] rounded-[20px] p-6">
        @csrf
        <x-form.field class="text-center mb-6">
            <x-form.description title="Sign In" description="Start reviewing your favorite games today"></x-form.description>
        </x-form.field>
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="email">Email</x-form.label>
            <x-form.input type="email" name="email" id="email" placeholder="you@example.com" required/>
        </x-form.field>
        <x-form.field class="flex flex-col gap-2">
            <x-form.label for="password">Password</x-form.label>
            <x-form.input type="password" name="password" id="password" placeholder="******" minlenght="8" required/>
        </x-form.field>
        <x-form.field class="mt-3">
            <x-form.button>Sign In</x-form.button>
        </x-form.field>
        <form-field>
            <p class="text-center text-sm text-gray-400 mt-4">
                Não tem uma conta?
                <a href="/register" class="text-primary font-bold hover:underline">Registre-se</a>
            </p>
        </form-field>
    </x-form.form>
</x-layout>
