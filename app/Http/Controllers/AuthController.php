<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('Academy.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $remember_me = $request->has('remember') ? true : false;
        $credentials = $request->only('email', 'password');
        if (auth()->guard('academy')->attempt($credentials, $remember_me)) {
            return to_route('academy.index');
        }
        return redirect()->back()->with(['error' => trans('admin.auth.invalid_email_or_password')])->withInput($request->only('email'));
    }

    public function logout()
    {
        auth()->guard('academy')->logout();
        session()->invalidate();
        return to_route('academy.loginPage');
    }
}
