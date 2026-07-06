<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Show the login form.
     */
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Show the registration form.
     */
    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        // Rate limit: 3 attempts per minute
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => trans('auth.throttle', [
                        'seconds' => $seconds,
                        'minutes' => (int) ceil($seconds / 60),
                    ]),
                ]);
        }

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                RateLimiter::clear($throttleKey);

                $request->session()->regenerate();

                return redirect()->intended(route('profile.show', [
                    'user' => Auth::user(),
                ]));
            }
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey);

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => __('An unexpected error occurred. Please try again later.'),
                ]);
        }

        RateLimiter::hit($throttleKey);

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => trans('auth.failed'),
            ]);
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nickname' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,nickname'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',       // al menos una mayúscula
                'regex:/[a-z]/',       // al menos una minúscula
                'regex:/[0-9]/',       // al menos un número
            ],
        ]);

        try {
            $user = User::create([
                'nickname' => trim($validated['nickname']),
                'email'    => trim($validated['email']),
                'password' => Hash::make($validated['password']),
            ]);

            Auth::login($user);

            $request->session()->regenerate();

            return redirect()->route('profile.show', ['user' => $user]);
        } catch (\Exception $e) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'error' => __('An unexpected error occurred during registration. Please try again.'),
                ]);
        }
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        try {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Exception $e) {
            // Even if something fails, we want the user to be logged out
        }

        return redirect()->route('login');
    }
}
