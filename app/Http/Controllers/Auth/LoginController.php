<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create(){
        return view('auth.login');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(Auth::attempt($validated)){
            $request->session()->regenerate();

            if (! $request->user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended('/')->with('success', __('auth_ui.login_success'));
        }

        return back()->withErrors(['email' => __('auth_ui.invalid_credentials')])->onlyInput('email');
    }
}
