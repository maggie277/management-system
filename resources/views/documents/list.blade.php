@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<style>
.document-list-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f3fef6);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.document-list-card .card-header {
    background: linear-gradient(145deg, #198754, #157347);
    color: white;
    border-radius: 1rem 1rem 0 0 !important;
    border: none;
    padding: 1rem 1.5rem;
}
.search-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f8fff9);
    box-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
}
.category-badge {
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}
.category-badge.active {
    background: linear-gradient(145deg, #198754, #157347) !important;
}
.category-badge:not(.active):hover {
    background: rgba(25, 135, 84, 0.1) !important;
}
.btn-success {
    background: linear-gradient(145deg, #198754, #157347);
    border: none;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}
.btn-outline-success {
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-outline-success:hover {
    transform: translateY(-2px);
}
.badge-success {
    background: linear-gradient(145deg, #198754, #157347) !important;
}
.document-table {
    font-size: 0.875rem;
}
.document-table thead th {
    background: rgba(25, 135, 84, 0.1);
    color: #198754;
    font-weight: 600;
    border-bottom: 2px solid #198754;
    padding: 1rem 0.75rem;
}
.document-table tbody tr {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}
.document-table tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.05);
    border-left: 3px solid #198754;
    transform: translateX(2px);
}
.document-table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-bottom: 1px solid rgba(25, 135, 84, 0.1);
}
.file-type-badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
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
                <h4 class="page-title mb-1">
                    @if(isset($currentFolder))
                        <i class="bi bi-folder-fill text-warning me-2"></i>{{ $currentFolder->name }}
                    @elseif(isset($category))
                        <i class="bi bi-collection-fill text-success me-2"></i>{{ $category->name }} Documents
                    @else
                        <i class="bi bi-files me-2"></i>All Documents
                    @endif
                </h4>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('documents.index') }}">Documents</a></li>
                    @if(isset($category))
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    @else
                        <li class="breadcrumb-item active">All Documents</li>
                    @endif
                </ol>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('documents.create') }}" class="btn btn-success">
                        <i class="bi bi-cloud-upload me-1"></i> Upload
                    </a>
                    @if(isset($category) && !request('search'))
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                            <i class="bi bi-folder-plus me-1"></i> Folder
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Categories --}}
    <div class="card search-card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('documents.list') }}"
                           class="category-badge {{ !isset($category) ? 'active text-white' : 'bg-white text-success border' }} text-decoration-none">
                            <i class="bi bi-collection me-1"></i>All Documents ({{ $totalDocuments ?? 0 }})
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('documents.category', $cat) }}"
                               class="category-badge {{ isset($category) && $category->id == $cat->id ? 'active text-white' : 'bg-white text-success border' }} text-decoration-none">
                                <i class="bi bi-collection me-1"></i>{{ $cat->name }} ({{ $cat->documents_count }})
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <form action="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" method="GET" class="d-flex gap-2">
                        <input type="text" name="search" class="form-control" placeholder="Search documents..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Folders Section - ONLY SHOW WHEN NOT SEARCHING --}}
    @if(isset($category) && isset($subfolders) && $subfolders->count() > 0 && !request('search'))
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(145deg, #198754, #157347); color: white; border-radius: 1rem 1rem 0 0;">
                <h5 class="card-title mb-0 text-white">
                    <i class="bi bi-folder-symlink me-2"></i>Folders in {{ $category->name }}
                    <span class="badge bg-light text-success ms-2">{{ $subfolders->count() }} folders</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($subfolders as $folder)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 text-center" style="border-radius: 0.75rem; border: 1px solid rgba(25, 135, 84, 0.15);">
                                <div class="card-body">
                                    <i class="bi bi-folder-fill text-warning display-6"></i>
                                    <h6 class="card-title mt-2 text-success">{{ $folder->name }}</h6>
                                    <p class="card-text small text-muted">
                                        <i class="bi bi-file-earmark me-1"></i>{{ $folder->documents_count }} documents
                                        <br>
                                        <i class="bi bi-folder me-1"></i>{{ $folder->children_count }} subfolders
                                    </p>
                                    @if($folder->description)
                                        <p class="card-text small">{{ Str::limit($folder->description, 60) }}</p>
                                    @endif
                                </div>
                                <div class="card-footer bg-transparent">
                                    <a href="{{ route('documents.category', ['category' => $category, 'folder_id' => $folder->id]) }}" class="btn btn-outline-success btn-sm w-100 mb-1">
                                        <i class="bi bi-folder2-open me-1"></i>Browse Folder
                                    </a>
                                    <small class="text-muted">Category: {{ $category->name }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Documents Table -- ALWAYS SHOW --}}
    <div class="card document-list-card">
        <div class="card-header">
            <h5 class="card-title mb-0 text-white">
                @if(request('search'))
                    <i class="bi bi-search me-2"></i>Search Results for "{{ request('search') }}"
                @elseif(isset($currentFolder))
                    <i class="bi bi-file-earmark-text me-2"></i>Documents in "{{ $currentFolder->name }}"
                @elseif(isset($category))
                    <i class="bi bi-file-earmark-text me-2"></i>Documents in {{ $category->name }}
                @else
                    <i class="bi bi-file-earmark-text me-2"></i>All Documents
                @endif
                <span class="badge bg-light text-success ms-2">{{ $documents->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover document-table mb-0">
                        <thead>
                            <tr>
                                <th width="25%">Document Title</th>
                                <th width="15%">Category</th>
                                <th width="15%">Folder</th>
                                <th width="10%">Type</th>
                                <th width="15%">Uploaded By</th>
                                <th width="10%">Date</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text text-success me-2"></i>
                                            <div>
                                                <strong class="text-success">{{ $document->title }}</strong>
                                                @if($document->description)
                                                    <br><small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($document->category_id && $document->documentCategory)
                                            <span class="badge badge-success">
                                                {{ $document->documentCategory->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($document->folder)
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-folder me-1"></i>{{ $document->folder->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge file-type-badge bg-secondary">
                                            {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $document->user->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $document->created_at->format('M j, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('documents.show', $document) }}" class="btn btn-outline-success" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder-x display-1 text-muted mb-3"></i>
                    <h4 class="text-muted">
                        @if(request('search'))
                            No documents found for "{{ request('search') }}"
                        @elseif(isset($currentFolder))
                            No documents found in this folder
                        @elseif(isset($category))
                            No documents found in {{ $category->name }} category
                        @else
                            No documents found
                        @endif
                    </h4>
                    <p class="text-muted mb-3">Upload your first document to get started</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success">
                        <i class="bi bi-cloud-upload me-1"></i> Upload Document
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
@if(isset($category) && !request('search'))
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-folder-plus me-2"></i>Create New Folder in {{ $category->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('folders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

                    <div class="mb-3">
                        <label class="form-label">Folder Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter folder name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional folder description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Create Folder</button>
                </div>
            </form>
        </div>

    </div>
</div>
@endif
@endsection
