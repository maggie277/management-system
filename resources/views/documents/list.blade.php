@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<style>
.document-list-card {
    border-radius: 0.5rem;
    border: 1px solid #e0e0e0;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.document-list-card .card-header {
    background: #f8f9fa;
    color: #198754;
    border-bottom: 2px solid #198754;
    border-radius: 0.5rem 0.5rem 0 0 !important;
    padding: 0.75rem 1rem;
    font-weight: 600;
}
.search-card {
    border-radius: 0.5rem;
    border: 1px solid #e0e0e0;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.category-badge {
    border-radius: 0.25rem;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
    margin: 0.1rem;
}
.category-badge.active {
    background: #198754 !important;
    color: white !important;
}
.category-badge:not(.active) {
    background: white;
    color: #198754;
    border: 1px solid #198754;
}
.category-badge:not(.active):hover {
    background: rgba(25, 135, 84, 0.1) !important;
}
.btn-success {
    background: #198754;
    border: none;
    border-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
.btn-success:hover {
    background: #157347;
    transform: none;
    box-shadow: none;
}
.btn-outline-success {
    border-radius: 0.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}
.btn-outline-success:hover {
    transform: none;
}
.badge-success {
    background: #198754 !important;
}
.document-table {
    font-size: 0.8rem;
    margin: 0;
}
.document-table thead th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 0.6rem 0.5rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.document-table tbody tr {
    transition: background-color 0.15s ease;
    border-bottom: 1px solid #dee2e6;
}
.document-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: none;
    border-left: none;
}
.document-table td {
    padding: 0.6rem 0.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #dee2e6;
    font-size: 0.8rem;
}
.file-type-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.folder-row {
    background-color: #fffbf0;
    cursor: pointer;
}
.folder-row:hover {
    background-color: #fef9e7 !important;
}
.search-highlight {
    background-color: #fff3cd !important;
    border-left: 3px solid #ffc107;
}
.search-highlight:hover {
    background-color: #ffeaa7 !important;
}
.compact-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}
.btn-group-sm > .btn {
    padding: 0.2rem 0.4rem;
    font-size: 0.7rem;
}
.table-responsive {
    border-radius: 0 0 0.5rem 0.5rem;
}
.breadcrumb-item a {
    text-decoration: none;
    color: #198754;
}
.breadcrumb-item.active {
    color: #6c757d;
}
.folder-breadcrumb {
    background: #f8f9fa;
    border-radius: 0.25rem;
    padding: 0.5rem 1rem;
    margin-bottom: 1rem;
}
.folder-path {
    font-size: 0.8rem;
    color: #6c757d;
}
.upload-to-folder-btn {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>

<div class="container-fluid">
    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="page-title-box py-2">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h4 class="page-title mb-1" style="font-size: 1.1rem;">
                    @if(isset($currentFolder))
                        <i class="bi bi-folder-fill text-warning me-2"></i>{{ $currentFolder->name }}
                    @elseif(isset($currentCategory))
                        <i class="bi bi-collection-fill text-success me-2"></i>{{ $currentCategory->name }} Documents
                    @else
                        <i class="bi bi-files me-2"></i>All Documents
                    @endif
                </h4>
                <ol class="breadcrumb m-0" style="font-size: 0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('documents.list') }}">Documents</a></li>
                    @if(isset($currentCategory))
                        @if(isset($currentFolder))
                            <li class="breadcrumb-item"><a href="{{ route('documents.category', $currentCategory) }}">{{ $currentCategory->name }}</a></li>
                            <li class="breadcrumb-item active">{{ $currentFolder->name }}</li>
                        @else
                            <li class="breadcrumb-item active">{{ $currentCategory->name }}</li>
                        @endif
                    @else
                        <li class="breadcrumb-item active">All Documents</li>
                    @endif
                </ol>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-flex gap-2 justify-content-end">
                    @if(isset($currentFolder))
                        <a href="{{ route('documents.create', ['folder_id' => $currentFolder->id, 'category_id' => $currentFolder->category_id]) }}"
                           class="btn btn-success upload-to-folder-btn">
                            <i class="bi bi-cloud-upload me-1"></i> Upload to this Folder
                        </a>
                    @elseif(isset($currentCategory))
                        <a href="{{ route('documents.create', ['category_id' => $currentCategory->id]) }}"
                           class="btn btn-success">
                            <i class="bi bi-cloud-upload me-1"></i> Upload
                        </a>
                    @else
                        <a href="{{ route('documents.create') }}" class="btn btn-success">
                            <i class="bi bi-cloud-upload me-1"></i> Upload
                        </a>
                    @endif

                    {{-- ALWAYS SHOW NEW FOLDER BUTTON --}}
                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                        <i class="bi bi-folder-plus me-1"></i> New Folder
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Search and Categories --}}
    <div class="card search-card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-1">
                        <a href="{{ route('documents.list') }}"
                           class="category-badge {{ !isset($currentCategory) ? 'active' : '' }}">
                            <i class="bi bi-collection me-1"></i>All Documents
                            <span class="badge bg-white text-success ms-1">{{ $totalDocuments }}</span>
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('documents.category', $cat) }}"
                               class="category-badge {{ isset($currentCategory) && $currentCategory->id == $cat->id ? 'active' : '' }}">
                                <i class="bi bi-collection me-1"></i>{{ $cat->name }}
                                <span class="badge bg-white text-success ms-1">{{ $cat->documents_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <form action="{{ isset($currentCategory) ? route('documents.category', $currentCategory) : route('documents.list') }}" method="GET" class="d-flex gap-1">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search documents and folders..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ isset($currentCategory) ? route('documents.category', $currentCategory) : route('documents.list') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Folder Breadcrumb --}}
    @if(isset($currentFolder))
    <div class="folder-breadcrumb">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('documents.category', $currentCategory) }}">
                        <i class="bi bi-collection me-1"></i>{{ $currentCategory->name }} Root
                    </a>
                </li>
                @if(isset($parentFolders))
                    @foreach($parentFolders as $parent)
                        <li class="breadcrumb-item">
                            <a href="{{ route('folders.show', ['category' => $currentCategory, 'folder' => $parent]) }}">
                                <i class="bi bi-folder me-1"></i>{{ $parent->name }}
                            </a>
                        </li>
                    @endforeach
                @endif
                <li class="breadcrumb-item active">
                    <i class="bi bi-folder-fill me-1"></i>{{ $currentFolder->name }}
                </li>
            </ol>
        </nav>
        @if($currentFolder->description)
            <div class="folder-path mt-1">
                <small class="text-muted">{{ $currentFolder->description }}</small>
            </div>
        @endif
    </div>
    @endif

    {{-- Combined Folders and Documents Table --}}
    <div class="card document-list-card">
        <div class="card-header">
            <h5 class="card-title mb-0" style="font-size: 0.9rem;">
                @if(request('search'))
                    <i class="bi bi-search me-2"></i>Search Results for "{{ request('search') }}"
                @elseif(isset($currentFolder))
                    <i class="bi bi-folder me-2"></i>Contents of "{{ $currentFolder->name }}"
                @elseif(isset($currentCategory))
                    <i class="bi bi-collection me-2"></i>Contents of "{{ $currentCategory->name }}"
                @else
                    <i class="bi bi-files me-2"></i>All Documents and Folders
                @endif
                <span class="badge bg-success ms-2" style="font-size: 0.7rem;">
                    @php
                        $totalItems = $documents->total() + (isset($subfolders) ? $subfolders->count() : 0);
                    @endphp
                    {{ $totalItems }} items
                </span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($subfolders->count() > 0 || $documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover document-table mb-0">
                        <thead>
                            <tr>
                                <th width="35%">Name</th>
                                <th width="12%">Type</th>
                                <th width="15%">Category</th>
                                <th width="10%">Items</th>
                                <th width="13%">Date</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Display Subfolders --}}
                            @foreach($subfolders as $folder)
                                <tr class="folder-row" onclick="window.location='{{ route('folders.show', ['category' => $currentCategory ? $currentCategory->id : $folder->category_id, 'folder' => $folder]) }}'">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-folder-fill text-warning me-2" style="font-size: 0.9rem; flex-shrink: 0;"></i>
                                            <div class="folder-info">
                                                <strong style="font-size: 0.8rem;">{{ $folder->name }}</strong>
                                                @if($folder->description)
                                                    <br><small class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($folder->description, 40) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge compact-badge bg-warning text-dark">
                                            <i class="bi bi-folder me-1"></i>Folder
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($folder->category)
                                            <span class="badge compact-badge badge-success">
                                                {{ $folder->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            {{ $folder->documents_count }} docs
                                            @if($folder->children_count > 0)
                                                <br>+ {{ $folder->children_count }} sub
                                            @endif
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $folder->updated_at->format('M j, Y') }}</small>
                                    </td>
                                    <td class="align-middle">
                                        <div class="btn-group btn-group-sm" onclick="event.stopPropagation()">
                                            @if($currentCategory)
                                                <a href="{{ route('folders.show', ['category' => $currentCategory, 'folder' => $folder]) }}"
                                                   class="btn btn-outline-success" title="Open Folder">
                                                    <i class="bi bi-folder2-open"></i>
                                                </a>
                                                <a href="{{ route('documents.create', ['folder_id' => $folder->id, 'category_id' => $currentCategory->id]) }}"
                                                   class="btn btn-success" title="Upload to this Folder">
                                                    <i class="bi bi-cloud-upload"></i>
                                                </a>
                                            @elseif($folder->category)
                                                <a href="{{ route('folders.show', ['category' => $folder->category, 'folder' => $folder]) }}"
                                                   class="btn btn-outline-success" title="Open Folder">
                                                    <i class="bi bi-folder2-open"></i>
                                                </a>
                                            @else
                                                <span class="btn btn-outline-secondary" title="Folder has no category">
                                                    <i class="bi bi-folder2-open"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Display Documents --}}
                            @foreach($documents as $document)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text text-success me-2" style="font-size: 0.9rem; flex-shrink: 0;"></i>
                                            <div class="document-info">
                                                <strong style="font-size: 0.8rem; color: #198754;">
                                                    {{ $document->title }}
                                                </strong>
                                                @if($document->description)
                                                    <br><small class="text-muted" style="font-size: 0.7rem;">
                                                        {{ Str::limit($document->description, 40) }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge file-type-badge bg-secondary">
                                            {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @if($document->category_id && $document->documentCategory)
                                            <span class="badge compact-badge badge-success">
                                                {{ $document->documentCategory->name }}
                                            </span>
                                        @else
                                            <span class="badge compact-badge bg-secondary">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted" style="font-size: 0.8rem;">—</small>
                                    </td>
                                    <td class="align-middle">
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $document->created_at->format('M j, Y') }}</small>
                                    </td>
                                    <td class="align-middle">
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

                {{-- PAGINATION REMOVED --}}

            @else
                <div class="text-center py-4">
                    <i class="bi bi-folder-x text-muted mb-2" style="font-size: 2rem;"></i>
                    <h6 class="text-muted">
                        @if(request('search'))
                            No documents or folders found for "{{ request('search') }}"
                        @elseif(isset($currentFolder))
                            No documents or folders found in this folder
                        @elseif(isset($currentCategory))
                            No documents or folders found in {{ $currentCategory->name }} category
                        @else
                            No documents or folders found
                        @endif
                    </h6>
                    <p class="text-muted mb-2" style="font-size: 0.8rem;">Upload your first document to get started</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-cloud-upload me-1"></i> Upload Document
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                        <i class="bi bi-folder-plus me-1"></i> Create Folder
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" style="font-size: 1rem;">
                    <i class="bi bi-folder-plus me-2"></i>
                    @if(isset($currentFolder))
                        Create New Folder in "{{ $currentFolder->name }}"
                    @elseif(isset($currentCategory))
                        Create New Folder in "{{ $currentCategory->name }}"
                    @else
                        Create New Folder
                    @endif
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('folders.store') }}" method="POST" id="createFolderForm">
                @csrf
                <div class="modal-body">
                    @if(isset($currentFolder))
                        <input type="hidden" name="category_id" value="{{ $currentFolder->category_id }}">
                        <input type="hidden" name="parent_id" value="{{ $currentFolder->id }}">
                    @elseif(isset($currentCategory))
                        <input type="hidden" name="category_id" value="{{ $currentCategory->id }}">
                    @else
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.875rem;">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select form-select-sm" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.875rem;">Folder Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Enter folder name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.875rem;">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Optional folder description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm">
                        Create Folder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.folder-row').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', function() {
            const onclickAttr = this.getAttribute('onclick');
            if (onclickAttr) {
                const urlMatch = onclickAttr.match(/'([^']+)'/);
                if (urlMatch && urlMatch[1]) {
                    window.location.href = urlMatch[1];
                }
            }
        });
    });
});
</script>
@endsection
@endsection
