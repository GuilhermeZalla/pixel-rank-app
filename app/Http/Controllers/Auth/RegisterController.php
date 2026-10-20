<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Jobs\NewUserNotification;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $user->sendEmailVerificationNotification();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')->with('success', __('auth_ui.registration_success'));
    }

}
