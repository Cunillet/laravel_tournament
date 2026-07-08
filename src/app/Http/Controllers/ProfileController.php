<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Display the specified user's profile.
     */
    public function show(User $user): Response
    {
        // Only allow viewing your own profile
        if (Auth::id() !== $user->id) {
            abort(403, __('You can only view your own profile.'));
        }

        return Inertia::render('Profile/Show');
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit(User $user): Response
    {
        // Only allow editing your own profile
        if (Auth::id() !== $user->id) {
            abort(403, __('You can only edit your own profile.'));
        }

        return Inertia::render('Profile/Edit');
    }

    /**
     * Update the user's profile.
     */
    public function update(UpdateProfileRequest $request, User $user): RedirectResponse
    {
        // Only allow updating your own profile
        if (Auth::id() !== $user->id) {
            abort(403, __('You can only update your own profile.'));
        }

        $validated = $request->validated();

        try {
            $user->update([
                'nickname' => trim($validated['nickname']),
                'email'    => trim($validated['email']),
            ]);

            return redirect()
                ->back()
                ->with('success', __('Profile updated successfully.'));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'error' => __('An unexpected error occurred. Please try again.'),
                ]);
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user): RedirectResponse
    {
        if (Auth::id() !== $user->id) {
            abort(403, __('You can only update your own password.'));
        }

        $validated = $request->validated();

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()
                ->back()
                ->with('success', __('Password updated successfully.'));
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => __('An unexpected error occurred. Please try again.'),
            ]);
        }
    }
}
