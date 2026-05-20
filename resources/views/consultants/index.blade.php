@extends('layouts.app')

@section('title', 'Consultants Management')

@section('content')
<style>
.stat-card {
    transition: all 0.35s ease;
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    min-height: 180px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.stat-card:hover {
    transform: translateY(-6px) scale(1.02);
    box-shadow: 0 10px 20px rgba(25, 135, 84, 0.2);
    background: linear-gradient(145deg, #e9fff0, #ffffff);
}
.stat-value {
    font-size: 2rem;
    font-weight: 700;
    color: #198754;
    letter-spacing: 0.5px;
    line-height: 1.2;
    word-break: break-word;
}
.stat-label {
    color: #3a3a3a;
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 1px;
    margin-top: 0.5rem;
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="page-title mb-1" style="color: #198754; font-weight: 700;">Consultants Management</h4>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('consultants.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle me-1"></i> Add New Consultant
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Row -->
            <div class="row mt-4">
                <div class="col-md-6 mb-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-value">{{ $totalConsultants }}</div>
                        <div class="stat-label">Total Consultants</div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="stat-card text-center p-4">
                        <div class="stat-value">{{ $activeConsultants }}</div>
                        <div class="stat-label">Active Consultants</div>
                    </div>
                </div>
            </div>

            <!-- Consultants Table -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3" style="color: #198754; font-weight: 600;">Consultants List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Specialization</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($consultants as $consultant)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $consultant->name }}</td>
                                    <td>{{ $consultant->email }}</td>
                                    <td>{{ $consultant->phone ?? '-' }}</td>
                                    <td>{{ $consultant->specialization ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $consultant->status == 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($consultant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('consultants.show', $consultant) }}" class="btn btn-sm btn-outline-success" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('consultants.edit', $consultant) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('consultants.destroy', $consultant) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            <a href="{{ route('consultants.toggle-status', $consultant) }}" class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-people text-muted d-block mb-2" style="font-size: 2rem;"></i>
                                        <span class="text-muted">No consultants found.</span>
                                        <div class="mt-2">
                                            <a href="{{ route('consultants.create') }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-plus-circle me-1"></i> Add Your First Consultant
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $consultants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
