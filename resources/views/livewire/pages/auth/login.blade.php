<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTPD Portal - Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center min-vh-100 align-items-center">
            <div class="col-md-6 col-lg-5">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 80px; height: 80px;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h1 class="h2 fw-bold text-dark">CTPD Portal</h1>
                    <p class="text-muted">Centre for Trade Policy and Development</p>
                </div>

                <!-- Login Form -->
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h2 class="h4 text-center mb-4">Sign in to your account</h2>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label">Email address</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ old('email') }}"
                                       required
                                       autofocus
                                       placeholder="Enter your email">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       required
                                       placeholder="Enter your password">
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <!-- Submit -->
                            <button type="submit" class="btn btn-success w-100 py-2">
                                Sign In
                            </button>
                        </form>



                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="small text-muted">
                        © 2025 Centre for Trade Policy and Development. All rights reserved.<br>
                        Powered by Fortress Hub Technologies
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
