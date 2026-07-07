@extends('layouts.app')

@section('title', __('Profile - :name', ['name' => $user->nickname]))

@section('content')
<div class="card">
    <h1 class="card-title">{{ __('My Profile') }}</h1>

    <div class="profile-info">
        <dl>
            <dt>{{ __('Nickname') }}</dt>
            <dd>{{ $user->nickname }}</dd>

            <dt>{{ __('Email') }}</dt>
            <dd>{{ $user->email }}</dd>

            <dt>{{ __('Member since') }}</dt>
            <dd>{{ $user->created_at->format('d/m/Y H:i') }}</dd>

            <dt>{{ __('Last update') }}</dt>
            <dd>{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>

    <div class="profile-actions">
        <a href="{{ route('profile.edit', ['user' => $user]) }}" class="btn btn-secondary">
            {{ __('Edit Profile') }}
        </a>
    </div>
</div>
@endsection
