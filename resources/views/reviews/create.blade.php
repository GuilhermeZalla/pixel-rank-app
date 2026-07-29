<x-layout>
    <x-slot:title>Create Review</x-slot:title>
    <x-form.form method="POST" action="/reviews" class="m-[6vh] w-[50%]">
        @csrf
        <div class="flex flex-col gap-5">
            <x-form.field class="text-center mb-1">
                <x-form.description title="Create Review"
                    description="Share your opinion on a game!"></x-form.description>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="title">Title</x-form.label>
                <x-form.input type="title" name="title" id="title" placeholder="Your review title" required />
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="name">Recommendation</x-form.label>
                <div class="flex flex-row flex-wrap gap-3">
                    <x-form.input type="checkbox" placeholder="Recommended" />
                    <x-form.input type="checkbox" placeholder="Not Recommended" />
                    <x-form.input type="checkbox" placeholder="Mixed" />
                    <x-form.input type="checkbox" placeholder="Essential" />
                </div>
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="bio">Review</x-form.label>
                <x-form.input type="textarea" name="bio" id="bio" placeholder="Tell us about yourself" rows="15" />
            </x-form.field>
            <x-form.field class="flex flex-col gap-2">
                <x-form.label for="email">Email</x-form.label>
                <x-form.input type="email" name="email" id="email" placeholder="you@example.com" required />
            </x-form.field>
            <x-form.field class="mt-2">
                <x-form.button>Create</x-form.button>
            </x-form.field>
        </div>
    </x-form.form>
</x-layout>