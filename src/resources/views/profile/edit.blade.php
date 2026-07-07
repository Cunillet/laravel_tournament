@extends('layouts.app')

@section('title', __('Edit Profile'))

@section('content')
<div class="card">
    <h1 class="card-title">{{ __('Edit Profile') }}</h1>

    <!-- Profile Info Form -->
    <form method="POST" action="{{ route('profile.update', ['user' => $user]) }}">
        @csrf

        <div class="form-group">
            <label for="nickname" class="form-label">{{ __('Nickname') }}</label>
            <input
                id="nickname"
                type="text"
                name="nickname"
                class="form-input @error('nickname') error @enderror"
                value="{{ old('nickname', $user->nickname) }}"
                required
                maxlength="50"
            >
            @error('nickname')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input
                id="email"
                type="email"
                name="email"
                class="form-input @error('email') error @enderror"
                value="{{ old('email', $user->email) }}"
                required
                maxlength="255"
            >
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="current_password" class="form-label">{{ __('Current Password (required to save changes)') }}</label>
            <input
                id="current_password"
                type="password"
                name="current_password"
                class="form-input @error('current_password') error @enderror"
                required
            >
            @error('current_password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn">
            {{ __('Save Changes') }}
        </button>
    </form>

    <hr style="border: none; border-top: 1px solid #334155; margin: 2rem 0;">

    <!-- Change Password Form -->
    <h2 style="font-size:1.2rem; color:#f1f5f9; margin-bottom:1rem;">{{ __('Change Password') }}</h2>

    <form method="POST" action="{{ route('profile.update-password', ['user' => $user]) }}">
        @csrf

        <div class="form-group">
            <label for="cp_current_password" class="form-label">{{ __('Current Password') }}</label>
            <input
                id="cp_current_password"
                type="password"
                name="current_password"
                class="form-input @error('current_password') error @enderror"
                required
            >
        </div>

        <div class="form-group">
            <label for="new_password" class="form-label">{{ __('New Password') }}</label>
            <input
                id="new_password"
                type="password"
                name="password"
                class="form-input @error('password') error @enderror"
                required
                autocomplete="new-password"
            >
            <p class="field-error" style="color:#64748b;font-size:0.75rem;margin-top:0.25rem;">
                {{ __('Minimum 8 characters with uppercase, lowercase and numbers.') }}
            </p>
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="new_password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
            <input
                id="new_password_confirmation"
                type="password"
                name="password_confirmation"
                class="form-input"
                required
                autocomplete="new-password"
            >
        </div>

        <button type="submit" class="btn btn-danger">
            {{ __('Update Password') }}
        </button>
    </form>

    <div class="auth-links" style="margin-top:1.5rem;">
        <a href="{{ route('profile.show', ['user' => $user]) }}">{{ __('Back to profile') }}</a>
    </div>
</div>
@endsection
