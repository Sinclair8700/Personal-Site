<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function signInForm()
    {
        return view('account.sign-in', ['title' => 'Sign In']);
    }

    public function signIn(Request $request)
    {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($request->only('email', 'password'))) {
                return redirect()->route('dashboard');
            }

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    public function signUpForm()
    {
        return view('account.sign-up', ['title' => 'Sign Up']);
    }

    public function signOut()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function signUp(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|unique:users|max:255',
            'password' => ['required', 'confirmed', 'string', Rules\Password::default()],
            'name' => 'required|string|max:255',
        ]);

        User::create($data);
        return redirect()->route('login');
    }


}
