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

        <style>
            .sidebar {
                min-height: calc(100vh - 76px);
                background: #2c3e50;
            }
            .sidebar .nav-link {
                color: #ecf0f1;
                padding: 12px 20px;
                border-radius: 5px;
                margin: 2px 10px;
            }
            .sidebar .nav-link:hover, .sidebar .nav-link.active {
                background: #34495e;
                color: #3498db;
            }
            .sidebar-section-title {
                color: #95a5a6;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin: 10px 20px 5px;
                font-weight: bold;
            }
        </style>
    </head>
    <body class="bg-light">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark ctpd-bg-primary">
            <div class="container-fluid">
                <button class="navbar-toggler me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                    <i class="bi bi-building me-2"></i>CTPD Portal
                </a>

                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <div class="dropdown-header">
                                    <small class="text-muted">{{ Auth::user()->position }}</small><br>
                                    <strong>{{ Auth::user()->department }}</strong>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3 col-lg-2 d-none d-md-block bg-dark sidebar">
                    <div class="position-sticky pt-3">
                        <!-- User Info -->
                        <div class="text-center text-white p-3 mb-3">
                            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center mb-2"
                                 style="width: 60px; height: 60px;">
                                <i class="bi bi-person-fill fs-4 text-white"></i>
                            </div>
                            <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                            <small class="text-white-50">{{ Auth::user()->position }}</small>
                        </div>

                        <hr class="text-white-50">

                        <!-- Dynamic Navigation Menu -->
                        <ul class="nav flex-column">

                            {{-- Shared Section (everyone sees Dashboard only) --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                   href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i>
                                    Dashboard
                                </a>
                            </li>

                            {{-- ========= CALENDAR SECTION (ACCESSIBLE TO EVERYONE) ========= --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}"
                                   href="{{ route('calendar.index') }}">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    Calendar
                                </a>
                            </li>

                           {{-- ========= INSTITUTIONAL DOCUMENTS SECTION (ACCESSIBLE TO EVERYONE) ========= --}}
                            <hr class="text-white-50">
                            <div class="sidebar-section-title">Resources</div>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('documents.institutional.*') ? 'active' : '' }}"
                                   href="{{ route('documents.institutional.index') }}">
                                    <i class="bi bi-folder-fill me-2"></i>
                                    Institutional Documents
                                </a>
                            </li>

                            {{-- ========= SYSTEM ADMIN SECTION ========= --}}
                            @if(Auth::user()->role === 'system_admin')
                                <hr class="text-white-50">
                                <div class="sidebar-section-title">System Administration</div>

                                {{-- Budgets for System Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
                                       href="{{ route('budgets.index') }}">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Budgets
                                    </a>
                                </li>

                                {{-- Expenses for System Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                                       href="{{ route('expenses.index') }}">
                                        <i class="bi bi-wallet2 me-2"></i>
                                        Expenses
                                    </a>
                                </li>



                                {{-- All Documents for System Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                       href="{{ route('documents.index') }}">
                                        <i class="bi bi-folder me-2"></i>
                                        All Documents
                                    </a>
                                </li>

                                {{-- Assets for System Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}"
                                       href="{{ route('assets.index') }}">
                                        <i class="bi bi-box-seam me-2"></i>
                                        Assets
                                    </a>
                                </li>

                                {{-- Donors for System Admin --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('donors.*') ? 'active' : '' }}"
                                       href="{{ route('donors.index') }}">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Donors
                                    </a>
                                </li>

                                {{-- Optional: User Management for System Admin --}}
                                {{--
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                                       href="{{ route('users.index') }}">
                                        <i class="bi bi-people me-2"></i>
                                        User Management
                                    </a>
                                </li>
                                --}}

                            {{-- ========= MANAGEMENT SECTION (for non-system_admin management) ========= --}}
                            @elseif(in_array(Auth::user()->position, [
                                'Executive Director',
                                'Head of Research',
                                'Head of Advocacy and Campaigns',
                                'M&E Specialist',
                                'Fundraising and Partnership Manager',
                                'Human Resource Manager'
                            ]))
                                <hr class="text-white-50">
                                <div class="sidebar-section-title">Management</div>

                                {{-- Documents - Management only --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                       href="{{ route('documents.index') }}">
                                        <i class="bi bi-folder me-2"></i>
                                        Documents
                                    </a>
                                </li>

                                {{-- Assets & Donors visible for HR & specific positions --}}
                                @if(
                                    Auth::user()->department === 'Finance and Admin' ||
                                    Auth::user()->position === 'Human Resource Manager' ||
                                    Auth::user()->position === 'Executive Director' ||
                                    Auth::user()->position === 'Fundraising and Partnership Manager'
                                )
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}"
                                           href="{{ route('assets.index') }}">
                                            <i class="bi bi-box-seam me-2"></i>
                                            Assets
                                        </a>
                                    </li>

                                    {{-- DONORS LINK for Management --}}
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('donors.*') ? 'active' : '' }}"
                                           href="{{ route('donors.index') }}">
                                            <i class="bi bi-cash-coin me-2"></i>
                                            Donors
                                        </a>
                                    </li>
                                @endif

                                {{-- Budgets for Executive Director and Fundraising Manager --}}
                                @if(Auth::user()->position === 'Executive Director' ||
                                    Auth::user()->position === 'Fundraising and Partnership Manager')
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
                                           href="{{ route('budgets.index') }}">
                                            <i class="bi bi-cash-coin me-2"></i>
                                            Budgets
                                        </a>
                                    </li>
                                @endif
                            @endif

                            {{-- ========= FINANCE SECTION ========= --}}
                            @if(Auth::user()->department === 'Finance and Admin' && Auth::user()->role !== 'system_admin')
                                <hr class="text-white-50">
                                <div class="sidebar-section-title">Finance & Admin</div>

                                {{-- Finance-specific items (make them clickable) --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}"
                                       href="{{ route('budgets.index') }}">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Budgets
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}"
                                       href="{{ route('expenses.index') }}">
                                        <i class="bi bi-wallet2 me-2"></i>
                                        Expenses
                                    </a>
                                </li>



                                {{-- Assets & Donors also visible for Finance --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}"
                                       href="{{ route('assets.index') }}">
                                        <i class="bi bi-box-seam me-2"></i>
                                        Assets
                                    </a>
                                </li>

                                {{-- DONORS LINK for Finance --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('donors.*') ? 'active' : '' }}"
                                       href="{{ route('donors.index') }}">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Donors
                                    </a>
                                </li>
                            @endif

                            {{-- ========= OTHER DEPARTMENTS ========= --}}
                            @if(
                                Auth::user()->role !== 'system_admin' &&
                                !in_array(Auth::user()->department, ['Finance and Admin']) &&
                                !in_array(Auth::user()->position, [
                                    'Executive Director',
                                    'Head of Research',
                                    'Head of Advocacy and Campaigns',
                                    'M&E Specialist',
                                    'Fundraising and Partnership Manager',
                                    'Human Resource Manager'
                                ])
                            )
                                <hr class="text-white-50">
                                <div class="sidebar-section-title">Department</div>

                                <li class="nav-item">
                                    <span class="nav-link disabled">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Department Tools
                                    </span>
                                </li>

                                {{-- Optional: Add Donors link for other departments if needed --}}
                                {{--
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('donors.*') ? 'active' : '' }}"
                                       href="{{ route('donors.index') }}">
                                        <i class="bi bi-cash-coin me-2"></i>
                                        Donors
                                    </a>
                                </li>
                                --}}
                            @endif

                            {{-- ========= DEBUG INFORMATION (Remove in production) ========= --}}
                            {{--
                            <div style="display: none;">
                                <!-- Debug info to check user permissions -->
                                User Department: {{ Auth::user()->department }}<br>
                                User Position: {{ Auth::user()->position }}<br>
                                User Role: {{ Auth::user()->role }}<br>
                                Is Finance: {{ Auth::user()->department === 'Finance and Admin' ? 'Yes' : 'No' }}<br>
                                Is Management: {{ in_array(Auth::user()->position, ['Executive Director', 'Head of Research', 'Head of Advocacy and Campaigns', 'M&E Specialist', 'Fundraising and Partnership Manager', 'Human Resource Manager']) ? 'Yes' : 'No' }}
                            </div>
                            --}}

                        </ul>
                    </div>
                </div>

                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Bootstrap & Livewire Scripts -->
        @vite(['resources/js/app.js'])
        @livewireScripts

        <!-- Modals Stack -->
        @stack('modals')
    </body>
</html>
