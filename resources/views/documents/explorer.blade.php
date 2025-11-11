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
}
.category-badge.active {
    background: #198754 !important;
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
.pagination {
    font-size: 0.8rem;
    margin: 0;
}
.page-link {
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
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
                    @elseif(isset($category))
                        <i class="bi bi-collection-fill text-success me-2"></i>{{ $category->name }} Documents
                    @else
                        <i class="bi bi-files me-2"></i>All Documents
                    @endif
                </h4>
                <ol class="breadcrumb m-0" style="font-size: 0.8rem;">
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
    <div class="card search-card mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-1">
                        <a href="{{ route('documents.list') }}"
                           class="category-badge {{ !isset($category) ? 'active text-white' : 'bg-white text-success border' }} text-decoration-none">
                            <i class="bi bi-collection me-1"></i>All Documents
                        </a>
                        @foreach($categories as $cat)
                            <a href="{{ route('documents.category', $cat) }}"
                               class="category-badge {{ isset($category) && $category->id == $cat->id ? 'active text-white' : 'bg-white text-success border' }} text-decoration-none">
                                <i class="bi bi-collection me-1"></i>{{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <form action="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" method="GET" class="d-flex gap-1">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search documents and folders..."
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Combined Folders and Documents Table --}}
    <div class="card document-list-card">
        <div class="card-header">
            <h5 class="card-title mb-0" style="font-size: 0.9rem;">
                @if(request('search'))
                    <i class="bi bi-search me-2"></i>Search Results for "{{ request('search') }}"
                @elseif(isset($currentFolder))
                    <i class="bi bi-file-earmark-text me-2"></i>Contents of "{{ $currentFolder->name }}"
                @elseif(isset($category))
                    <i class="bi bi-file-earmark-text me-2"></i>Contents of "{{ $category->name }}"
                @else
                    <i class="bi bi-file-earmark-text me-2"></i>All Documents and Folders
                @endif
                <span class="badge bg-success ms-2" style="font-size: 0.7rem;">
                    @php
                        $foldersToShow = $subfolders ?? ($content['folders'] ?? collect());
                        $documentsToShow = $documents ?? ($content['documents'] ?? collect());

                        $searchTerm = request('search');

                        // If searching, only show documents that match the search
                        if ($searchTerm) {
                            $allItems = $documentsToShow->filter(function($document) use ($searchTerm) {
                                return stripos($document->title, $searchTerm) !== false ||
                                       ($document->description && stripos($document->description, $searchTerm) !== false);
                            });
                        } else {
                            // Normal view: show folders and documents mixed
                            $allItems = $foldersToShow->concat($documentsToShow);
                        }

                        $totalItems = $allItems->count();
                        $perPage = 10;
                        $currentPage = request('page', 1);
                        $start = ($currentPage - 1) * $perPage;
                        $paginatedItems = $allItems->slice($start, $perPage);
                        $totalPages = ceil($totalItems / $perPage);
                    @endphp
                    {{ $totalItems }}
                </span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($totalItems > 0)
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
                            @foreach($paginatedItems as $item)
                                @php
                                    $isSearchMatch = false;
                                    if ($searchTerm) {
                                        $isSearchMatch = stripos($item->title, $searchTerm) !== false ||
                                                       ($item->description && stripos($item->description, $searchTerm) !== false);
                                    }
                                @endphp

                                @if(isset($item->documents_count) && !$searchTerm) {{-- It's a folder (only show when not searching) --}}
                                    <tr class="folder-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-folder-fill text-warning me-2" style="font-size: 0.9rem;"></i>
                                                <div>
                                                    <strong style="font-size: 0.8rem;">{{ $item->name }}</strong>
                                                    @if($item->description)
                                                        <br><small class="text-muted" style="font-size: 0.7rem;">{{ Str::limit($item->description, 40) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge compact-badge bg-warning text-dark">
                                                <i class="bi bi-folder me-1"></i>Folder
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->category)
                                                <span class="badge compact-badge badge-success">
                                                    {{ $item->category->name }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $item->documents_count }} docs
                                                @if($item->children_count > 0)
                                                    <br>+ {{ $item->children_count }} sub
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $item->updated_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if(isset($category))
                                                    <a href="{{ route('documents.category', ['category' => $category, 'folder_id' => $item->id]) }}"
                                                       class="btn btn-outline-success" title="Open Folder">
                                                        <i class="bi bi-folder2-open"></i>
                                                    </a>
                                                @elseif($item->category)
                                                    <a href="{{ route('documents.category', ['category' => $item->category, 'folder_id' => $item->id]) }}"
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
                                @elseif(!isset($item->documents_count)) {{-- It's a document --}}
                                    <tr class="{{ $isSearchMatch ? 'search-highlight' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-file-earmark-text text-success me-2" style="font-size: 0.9rem;"></i>
                                                <div>
                                                    <strong style="font-size: 0.8rem; color: #198754;">
                                                        @if($isSearchMatch && $searchTerm)
                                                            {!! preg_replace("/($searchTerm)/i", '<mark class="bg-warning p-0">$1</mark>', e($item->title)) !!}
                                                        @else
                                                            {{ $item->title }}
                                                        @endif
                                                    </strong>
                                                    @if($item->description)
                                                        <br><small class="text-muted" style="font-size: 0.7rem;">
                                                            @if($isSearchMatch && $searchTerm)
                                                                {!! preg_replace("/($searchTerm)/i", '<mark class="bg-warning p-0">$1</mark>', Str::limit(e($item->description), 40)) !!}
                                                            @else
                                                                {{ Str::limit($item->description, 40) }}
                                                            @endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge file-type-badge bg-secondary">
                                                {{ strtoupper(pathinfo($item->file_path, PATHINFO_EXTENSION)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->category_id && $item->documentCategory)
                                                <span class="badge compact-badge badge-success">
                                                    {{ $item->documentCategory->name }}
                                                </span>
                                            @else
                                                <span class="badge compact-badge bg-secondary">Uncategorized</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted" style="font-size: 0.8rem;">—</small>
                                        </td>
                                        <td>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('M j, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('documents.show', $item) }}" class="btn btn-outline-success" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('documents.download', $item) }}" class="btn btn-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <a href="{{ route('documents.edit', $item) }}" class="btn btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($totalPages > 1)
                <div class="card-footer bg-white border-top-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            @php
                                $start = (($currentPage - 1) * $perPage) + 1;
                                $end = min($currentPage * $perPage, $totalItems);
                            @endphp
                            Showing {{ $start }} to {{ $end }} of {{ $totalItems }} entries
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                {{-- Previous Page Link --}}
                                <li class="page-item {{ $currentPage == 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" aria-label="Previous">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>

                                {{-- Page Numbers --}}
                                @for($i = 1; $i <= $totalPages; $i++)
                                    @if($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2))
                                        <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                        </li>
                                    @elseif($i == $currentPage - 3 || $i == $currentPage + 3)
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    @endif
                                @endfor

                                {{-- Next Page Link --}}
                                <li class="page-item {{ $currentPage == $totalPages ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" aria-label="Next">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bi bi-folder-x text-muted mb-2" style="font-size: 2rem;"></i>
                    <h6 class="text-muted">
                        @if(request('search'))
                            No documents found for "{{ request('search') }}"
                        @elseif(isset($currentFolder))
                            No documents or folders found in this folder
                        @elseif(isset($category))
                            No documents or folders found in {{ $category->name }} category
                        @else
                            No documents or folders found
                        @endif
                    </h6>
                    <p class="text-muted mb-2" style="font-size: 0.8rem;">Upload your first document to get started</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success btn-sm">
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
                <h5 class="modal-title" style="font-size: 1rem;">
                    <i class="bi bi-folder-plus me-2"></i>Create New Folder in {{ $category->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('folders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

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
                    <button type="submit" class="btn btn-success btn-sm">Create Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
