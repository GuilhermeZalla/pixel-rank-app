<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
