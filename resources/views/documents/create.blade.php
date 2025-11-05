@extends('layouts.app')

@section('content')
<div class="container-fluid py-5" style="background-color: #f8f9fa;">
    <div class="row justify-content-center">
        <div class="col-md-8">

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

            <div class="card shadow-lg border-0" style="border-radius: 20px;">
                <div class="card-header text-white" style="background-color: #198754; border-radius: 20px 20px 0 0;">
                    <h5 class="mb-0"><i class="bi bi-upload me-2"></i>Upload Document</h5>
                </div>
                <div class="card-body bg-white p-4">
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control shadow-sm"
                                   value="{{ old('title') }}" placeholder="Enter document title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control shadow-sm" rows="3"
                                      placeholder="Brief description of the document">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select shadow-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Document File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control shadow-sm"
                                   accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx" required>
                            <div class="form-text">Accepted formats: PDF, Word, Excel, PowerPoint (Max: 10MB)</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('documents.list') }}" class="btn btn-outline-success fw-semibold custom-btn">
                                <i class="bi bi-arrow-left me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-success fw-semibold custom-btn">
                                <i class="bi bi-upload me-2"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Styles --}}
<style>
    body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
    .custom-btn { background-color: white; color: #198754; border: 2px solid #198754; border-radius: 10px; padding: 8px 20px; transition: all 0.3s ease; }
    .custom-btn:hover { background-color: #198754; color: white; transform: translateY(-1px); }
    .card { border-radius: 20px; }
    .form-control:focus, .form-select:focus { border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.25); }
    .alert-success { background-color: #e6f9ee; border-left: 5px solid #198754; color: #155724; border-radius: 10px; }
    .alert-danger { background-color: #fcebea; border-left: 5px solid #dc3545; color: #721c24; border-radius: 10px; }
</style>
@endsection
