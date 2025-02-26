<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'name' => 'required',
        ]);

        User::create($request->only('name', 'email', 'password'));
        return redirect()->route('login');
    }


}
