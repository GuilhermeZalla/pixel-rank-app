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
        return view('users.show');
    }

    public function edit(Request $request, User $user){
        $reviews = Auth::user()->reviews()->paginate(8);
        $comments = Auth::user()->comments()->paginate(10);
        return view('users.edit', ['reviews' => $reviews, 'comments' => $comments, 'menu' => $request->menu]);
    }

    public function store(Request $request){

    }

    public function update(UserRequest $request, User $user){
        $validated = $request->validated();
        $user = User::find($user);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->name = $validated['name'];
        $user->nickname = $validated['nickname'];
        $user->bio = $validated['bio'];
        $user->email = $validated['email'];
        $user->save;

        return redirect('/')->with('success', 'User updated');
    }

    public function destroy(Request $request, User $user){}

}
