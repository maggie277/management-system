@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Edit Document</h1>
            <p class="text-muted mb-0">Update document information and file</p>
        </div>
        <div>
            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-success">
                Back to View
            </a>
        </div>
    </div>

    {{-- Edit Form --}}
    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-8">
                {{-- Document Info --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Edit Document Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">Document Title <span class="text-success">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                       id="title" name="title" value="{{ old('title', $document->title) }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Category <span class="text-success">*</span></label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $document->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $document->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- File Update --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Update Document File</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            Current file: <strong>{{ $document->file_name }}</strong>
                            <br>
                            <small>Leave empty to keep current file or upload a new one.</small>
                        </div>

                        <label for="file" class="form-label">New Document File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".pdf,.doc,.docx,.xlsx,.xls,.pptx">
                        <div class="form-text mb-3">Accepted formats: PDF, Word, Excel, PowerPoint (Max: 10MB)</div>
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 mb-5">
                    <button type="submit" class="btn btn-success btn-lg flex-grow-1">Update Document</button>
                    <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-success btn-lg flex-grow-1">Cancel</a>
                </div>
            </div>

            {{-- Current File Info --}}
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Current File</h5>
                    </div>
                    <div class="card-body text-center">
                        <h6 class="mb-2">{{ $document->file_name }}</h6>
                        <small class="text-muted">Uploaded: {{ $document->created_at->format('M j, Y') }}</small>
                        <div class="mt-3">
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-success w-100">
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.card {
    border-radius: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 128, 0, 0.1);
}

input, select, textarea {
    transition: box-shadow 0.2s;
}

input:focus, select:focus, textarea:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 128, 0, 0.25);
}
</style>
@endsection
