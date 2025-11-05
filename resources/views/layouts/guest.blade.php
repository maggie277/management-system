<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CTPD Management System') }}</title>

        <!-- Bootstrap CSS -->
        @vite(['resources/css/app.css'])

        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="bg-light">
        <div class="container">
            <div class="row justify-content-center min-vh-100 align-items-center">
                <div class="col-md-8 col-lg-6">
                    <!-- CTPD Header -->
                    <div class="text-center mb-5">
                        <div class="ctpd-bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-building fs-2"></i>
                        </div>
                        <h1 class="h3 fw-bold text-dark">CTPD Portal</h1>
                        <p class="text-muted">Centre for Trade Policy and Development</p>
                    </div>

                    <!-- Page Content -->
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            {{ $slot }}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="text-center mt-4">
                        <p class="text-muted small">&copy; {{ date('Y') }} CTPD. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap & Livewire Scripts -->
        @vite(['resources/js/app.js'])
        @livewireScripts
    </body>
</html>
