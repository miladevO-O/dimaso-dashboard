<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->credentials();

        $user = User::where('user_email', $credentials['user_email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // xử lý hash custom
        $stored_hash = $user->user_pass;
        $password = $credentials['password'];

        if (strpos($stored_hash, '$wp') === 0) {
            $stored_hash = substr($stored_hash, 3); // bỏ prefix "$wp"
        }

        if (! Hash::check($password, $stored_hash)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
