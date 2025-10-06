@extends('layouts.auth')

@section('content')
<div class="w-full max-w-sm bg-white p-6 rounded shadow mx-auto">
    <h1 class="text-2xl font-bold text-green-900 text-center mb-4">
        Admin Management System
    </h1>
    <p class="text-green-700 text-center mb-6">
        Secure admins only
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" class="text-green-800 font-medium" />
            <x-text-input id="email" class="block mt-1 w-full border p-2 rounded"
                type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" class="text-green-800 font-medium" />
            <x-text-input id="password" class="block mt-1 w-full border p-2 rounded"
                type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600" />
        </div>

        <!-- Remember Me -->
        <div class="block mb-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-green-700 shadow-sm focus:ring-green-500"
                    name="remember">
                <span class="ms-2 text-sm text-green-700">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-center mt-4">
            <button type="submit"
                class="w-full bg-green-700 text-white py-2 rounded hover:bg-green-800 transition">
                {{ __('Log in') }}
            </button>
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <p class="mt-4 text-center text-sm text-green-700">
                <a href="{{ route('password.request') }}"
                   class="underline hover:text-green-900">
                    {{ __('Forgot your password?') }}
                </a>
            </p>
        @endif
    </form>
</div>
@endsection
