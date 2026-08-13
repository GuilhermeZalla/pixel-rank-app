<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Jobs\NewUserNotification;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request)
    {
        $validated = $request->validated();
        $path = $request->file('avatar')?->store('avatars', 'public');
        $user = User::create([
            'name' => $validated['name'],
            'nickname' => $validated['nickname'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'bio' => $validated['bio'],
            'avatar' => $path
        ]);

        NewUserNotification::dispatch($user);
        return redirect('/login')->with('success', 'Account created successfully!');
    }

}
