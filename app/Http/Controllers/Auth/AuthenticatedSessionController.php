<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $validated = Validator::make(
            $request->all(),
            [
                'username' => 'required',
                'password' => 'required',
            ],
            [
                'username.required' => 'Silakan isi email / username terlebih dahulu',
                'password.required' => 'Silakan isi kata sandi terlebih dahulu'
            ]
        );

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()]);
        } else {
            $credentials = $request->only('username', 'password');
            $user = User::where(function ($query) use ($credentials) {
                $query->where('email', $credentials['username'])
                    ->orWhere('telephone', $credentials['username']);
            })->first();

            if (!$user) {
                return response()->json(['NoUsername' => ['message' => 'Username tidak tersedia.']]);
            }

            if ($user->status == 1) {
                return response()->json(['NonActiveUsername' => ['message' => 'Akun Anda telah dinonaktifkan.']]);
            }

            if ($user && Hash::check($credentials['password'], $user->password)) {
                Auth::loginUsingId($user->id);
                if ($user->role == 'warehouse') {
                    return response()->json(['redirect' => route('dashboard.index')]);
                } else {
                    return response()->json(['redirect' => route('dashboard.index')]);
                }
            } else {
                return response()->json(['WrongPassword' => ['message' => 'Kata sandi salah.']]);
            }
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
