@extends('layouts.auth')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 text-center mb-6">Document & Asset Management System</h1>
<p class="text-gray-600 dark:text-gray-400 text-center mb-6">Secure access for staff and admins only</p>

<x-auth-session-status class="mb-4" :status="session('status')" />

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email -->
    <div class="mb-4">
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mb-4">
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Remember Me -->
    <div class="block mb-4">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
        </label>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ms-3 bg-indigo-600 hover:bg-indigo-700">
            {{ __('Log in') }}
        </x-primary-button>
    </div>

    <!-- Forgot Password -->
    @if (Route::has('password.request'))
        <p class="mt-4 text-center text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('password.request') }}" class="underline hover:text-gray-900 dark:hover:text-gray-100">
                {{ __('Forgot your password?') }}
            </a>
        </p>
    @endif
</form>
@endsection
