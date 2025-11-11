@extends('layouts.app')

@section('content')
<style>
.stat-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    transition: all 0.3s ease;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(25, 135, 84, 0.15);
}
.btn-success {
    background: linear-gradient(145deg, #198754, #157347);
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    padding: 0.75rem 1.5rem;
}
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}
.btn-outline-success {
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    padding: 0.75rem 1.5rem;
}
.btn-outline-success:hover {
    transform: translateY(-2px);
}
.badge.bg-success {
    background: linear-gradient(145deg, #198754, #157347) !important;
}
</style>

<div class="container-fluid">
    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-title-box py-2">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="page-title mb-1">Document Management System</h4>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('documents.create') }}" class="btn btn-success">
                        <i class="bi bi-cloud-upload me-1"></i> Upload Document
                    </a>
                    <a href="{{ route('documents.list') }}" class="btn btn-outline-success">
                        <i class="bi bi-list-ul me-1"></i> All Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Document Stats / Category Cards --}}
    <div class="row mb-4">
        @php $categories = \App\Models\Category::all(); @endphp
        @foreach($categories as $cat)
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('documents.category', $cat) }}" class="text-decoration-none">
                    <div class="card stat-card h-100 text-center p-3">
                        <h6 class="text-success mb-2">{{ $cat->name }}</h6>
                        <h3 class="mb-0">{{ $cat->documents()->count() }}</h3>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
