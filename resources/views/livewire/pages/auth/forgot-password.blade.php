<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');
        session()->flash('status', __($status));
        $this->dispatch('reset-link-sent');
    }
}; ?>

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 p-3 bg-success bg-opacity-10 border border-success rounded-3 text-success">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <div class="mb-4 p-3 bg-light rounded-3 border-start border-success border-4">
        <div class="d-flex align-items-start">
            <i class="bi bi-info-circle-fill text-success me-2 mt-1"></i>
            <p class="text-dark mb-0 small">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>
    </div>

    <form wire:submit="sendPasswordResetLink" class="vstack gap-3">
        <!-- Email Address -->
        <div>
            <label for="email" class="form-label fw-semibold text-dark">
                <i class="bi bi-envelope me-1 text-success"></i>
                {{ __('Email Address') }}
            </label>
            <input
                wire:model="email"
                id="email"
                type="email"
                class="form-control form-control-lg @error('email') is-invalid @enderror"
                name="email"
                required
                autofocus
                placeholder="Enter your registered email address"
                style="border-left: 3px solid #198754;"
            >
            @error('email')
                <div class="text-danger small mt-2 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-end mt-2">
            <button type="submit" class="btn btn-success px-4 py-2 fw-semibold" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-envelope-paper me-2"></i>
                    {{ __('Send Reset Link') }}
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    {{ __('Sending...') }}
                </span>
            </button>
        </div>
    </form>

    <div class="text-center mt-4 pt-2 border-top">
        <a href="{{ route('login') }}" class="text-decoration-none small text-success hover-text-success">
            <i class="bi bi-arrow-left me-1"></i>
            {{ __('Back to Login') }}
        </a>
    </div>
</div>
