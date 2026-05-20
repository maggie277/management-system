<div>
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($status)
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ $status }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="text-muted small mb-4">
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
    </div>

    <form wire:submit.prevent="sendPasswordResetLink">
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input type="email"
                   wire:model="email"
                   id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Enter your registered email"
                   required
                   autofocus>
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                <span wire:loading.remove>
                    <i class="bi bi-envelope-paper me-1"></i> Send Reset Link
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Sending...
                </span>
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('reset-link-sent', () => {
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 3000);
            });
        });
    </script>
    @endpush
</div>
