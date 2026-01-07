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
        return view('account.sign-in');
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
        

        return view('account.sign-in');
    }

    public function signUpForm()
    {
        return view('account.sign-up');
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
