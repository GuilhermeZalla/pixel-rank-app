<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function show(User $user){
        $reviews = $user->reviews()->with('user')->latest()->paginate(10);

        return view('users.show', ['user' => $user, 'reviews' => $reviews]);
    }

    public function edit(Request $request, User $user){
        $reviews = Auth::user()->reviews()->withCount('comments')->latest()->paginate(8);
        $comments = Auth::user()->comments()->with(['review', 'user'])->latest()->paginate(10);
        $menu = in_array($request->menu, ['menu1', 'menu2', 'menu3', 'menu4'], true) ? $request->menu : 'menu1';

        return view('users.edit', ['reviews' => $reviews, 'comments' => $comments, 'menu' => $menu]);
    }

    public function store(Request $request){

    }

    public function update(UserRequest $request, User $user){
        $validated = $request->validated();
        $oldAvatar = null;
        $emailChanged = isset($validated['email']) && $validated['email'] !== $user->email;

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            if ($path !== false) {
                $oldAvatar = $user->avatar;
                $user->avatar = $path;
            }
        }

        $user->fill(array_intersect_key($validated, array_flip(['name', 'nickname', 'email', 'bio'])));

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            return redirect()->route('verification.notice')->with('success', __('dashboard.email_changed'));
        }

        return redirect('/')->with('success', __('dashboard.profile_updated'));
    }

    public function destroy(Request $request, User $user){}

}
