<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $user = User::firstOrNew(['email' => 'test@example.com']);

        // Re-running this seeder never changes an existing account or password.
        if ($user->exists) {
            return;
        }

        $user->fill([
            'name' => 'Conta de demonstração',
            'nickname' => 'demo_'.Str::lower(Str::random(8)),
            'password' => Hash::make('password'),
        ]);
        $user->email_verified_at = now();
        $user->save();
    }
}
