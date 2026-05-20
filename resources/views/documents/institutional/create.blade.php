@extends('layouts.app')

@section('content')
<style>
:root {
    --primary-green: #198754;
    --light-green: #f3fef6;
    --hover-green: #157347;
}

.card {
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
    background: #ffffff;
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background: #ffffff;
}

.btn-success {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
}

.btn-success:hover {
    background-color: var(--hover-green);
    border-color: var(--hover-green);
}

.btn-outline-success {
    border-color: var(--primary-green);
    color: var(--primary-green);
}

.btn-outline-success:hover {
    background-color: var(--primary-green);
    color: #fff;
}

.text-success {
    color: var(--primary-green) !important;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.required-field::after {
    content: " *";
    color: #dc3545;
}
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h1 class="h4 mb-0 fw-bold text-success">Upload Institutional Document</h1>
                    <a href="{{ route('documents.institutional.index') }}" class="btn btn-outline-success">
                        Back to Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">Document Details</h5>
                </div>
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('documents.institutional.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold required-field">Document Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label fw-bold required-field">Category</label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Policies/Manual" {{ old('category') == 'Policies/Manual' ? 'selected' : '' }}>Policies/Manual</option>
                                        <option value="Templates" {{ old('category') == 'Templates' ? 'selected' : '' }}>Templates</option>
                                        <option value="Research Reports" {{ old('category') == 'Research Reports' ? 'selected' : '' }}>Research Reports</option>
                                        <option value="Program Reports" {{ old('category') == 'Program Reports' ? 'selected' : '' }}>Program Reports</option>
                                        <option value="Leave Form" {{ old('category') == 'Leave Form' ? 'selected' : '' }}>Leave Form</option>
                                        <option value="Retirement Form" {{ old('category') == 'Retirement Form' ? 'selected' : '' }}>Retirement Form</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="document" class="form-label fw-bold required-field">Document File</label>
                            <input type="file" class="form-control @error('document') is-invalid @enderror"
                                   id="document" name="document" required>
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT (Max 10MB)</small>
                            @error('document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-4">
                            <a href="{{ route('documents.institutional.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-success">Upload Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
