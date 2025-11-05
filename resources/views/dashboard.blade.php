@extends('layouts.app')

@section('title', 'Dashboard - CTPD Management System')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h3 class="card-title">Welcome, {{ Auth::user()->name }}!</h3>
            <p class="text-muted mb-0">
                {{ Auth::user()->position }} - {{ Auth::user()->department }}
            </p>

            <!-- Quick Stats -->
            <div class="row mt-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-people fs-1"></i>
                            <h5>Team</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-folder fs-1"></i>
                            <h5>Documents</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-box-seam fs-1"></i>
                            <h5>Assets</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-cash-coin fs-1"></i>
                            <h5>Donors</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Recent Activity</h5>
                    <p class="text-muted">Your CTPD management system is ready!</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        More features coming soon as we build your system module by module.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
