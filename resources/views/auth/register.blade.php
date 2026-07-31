<x-layout>
    <x-slot:title>Register</x-slot:title>
    <x-form.form method="POST" action="/register" class="m-[6vh] border border-[#88888833] bg-[#0E0E0E] rounded-[20px] p-6">
        @csrf
        <x-form.field class="text-center mb-6">
            <x-form.description title="Create Account"
                description="We just need some basic information to get you started."></x-form.description>
        </x-form.field>
        <x-form.field>
            <x-form.label for="name">Name</x-form.label>
            <x-form.input type="text" name="name" id="name" placeholder="Your Name" required/>
        </x-form.field>
         <x-form.field>
            <x-form.label for="name">Nickname</x-form.label>
            <x-form.input type="text" name="name" id="name" placeholder="This nickname will be displayed publicly on the site" required/>
        </x-form.field>
        <x-form.field>
            <x-form.label for="email">Email</x-form.label>
            <x-form.input type="email" name="email" id="email" placeholder="you@example.com" required/>
        </x-form.field>
        <div class="flex flex-row justify-between gap-3">
            <x-form.field class="flex-1/2">
                <x-form.label for="password">Password</x-form.label>
                <x-form.input type="password" name="password" id="password" placeholder="******" />
            </x-form.field>
            <x-form.field class="flex-1/2">
                <x-form.label for="password_confirmation">Confirm Password</x-form.label>
                <x-form.input type="password" name="password_confirmation" id="password_confirmation" placeholder="******"
                    required />
            </x-form.field>
        </div>
        <x-form.field>
            <x-form.label for="avatar">Profile Picture (optional)</x-form.label>
            <x-form.input type="file" name="avatar" id="avatar" />
        </x-form.field>
        <x-form.field>
            <x-form.label for="bio">Bio (optional)</x-form.label>
            <x-form.input type="textarea" name="bio" id="bio" placeholder="Tell us about yourself" />
        </x-form.field>
        <x-form.field class="mt-2">
            <x-form.button>Create Account</x-form.button>
        </x-form.field>
    </x-form.form>
</x-layout>