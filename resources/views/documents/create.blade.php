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
                    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="documentForm">
                        @csrf

                        <!-- Remove the "Current Location" alert completely -->
                        <!-- You don't need it - you have full control below -->

                        <!-- Basic Information -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Document Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control shadow-sm"
                                   value="{{ old('title') }}" placeholder="Enter document title" required>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control shadow-sm" rows="3"
                                      placeholder="Brief description of the document">{{ old('description') }}</textarea>
                        </div>

                        <!-- Category Selection (ALWAYS SHOW) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select shadow-sm" required id="categorySelect">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (old('category_id') == $category->id) || (isset($currentCategory) && $currentCategory && $currentCategory->id == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- SIMPLE FOLDER SELECTION - Load all folders at once -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Folder (Optional)</label>
                            <select name="current_folder_id" class="form-select shadow-sm" id="folderSelect">
                                <option value="">Select Folder (or leave empty for category root)</option>
                                @foreach($categories as $category)
                                    @php
                                        $categoryFolders = \App\Models\Folder::where('category_id', $category->id)
                                            ->whereNull('parent_id')
                                            ->get();
                                    @endphp
                                    @if($categoryFolders->count() > 0)
                                        <optgroup label="{{ $category->name }}">
                                            @foreach($categoryFolders as $folder)
                                                <option value="{{ $folder->id }}"
                                                    {{ (isset($currentFolder) && $currentFolder && $currentFolder->id == $folder->id) ? 'selected' : '' }}>
                                                    {{ $folder->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Select a folder to organize your document, or leave empty to place in category root
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Document File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control shadow-sm"
                                   accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx,.txt,.jpg,.jpeg,.png" required>
                            <div class="form-text">Accepted formats: PDF, Word, Excel, PowerPoint, Images, Text (Max: 10MB)</div>
                            @error('file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ $backUrl ?? route('documents.list') }}" class="btn btn-outline-success fw-semibold custom-btn">
                                <i class="bi bi-arrow-left me-2"></i>Back
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
    .alert-info { background-color: #e6f3ff; border-left: 5px solid #0d6efd; color: #055160; border-radius: 10px; }
</style>

{{-- SIMPLE JavaScript - No API calls needed --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple form validation
    document.getElementById('documentForm').addEventListener('submit', function(e) {
        const fileInput = this.querySelector('input[name="file"]');
        const categoryInput = this.querySelector('select[name="category_id"]');

        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select a file to upload.');
            return;
        }

        if (!categoryInput.value) {
            e.preventDefault();
            alert('Please select a category.');
            return;
        }
    });
});
</script>
@endsection
