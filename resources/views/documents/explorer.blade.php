@extends('layouts.app')

@section('content')
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">
                @if(isset($currentFolder))
                    <i class="bi bi-folder-fill text-warning me-2"></i>{{ $currentFolder->name }}
                @elseif(isset($category))
                    <i class="bi bi-collection-fill text-success me-2"></i>{{ $category->name }}
                @else
                    <i class="bi bi-hdd-stack me-2"></i>All Documents
                @endif
            </h1>

            {{-- Breadcrumbs --}}
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    @foreach($breadcrumbs as $breadcrumb)
                        @if(!$loop->last)
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb['url'] }}" class="text-success text-decoration-none">
                                    @if($loop->first)
                                        <i class="bi bi-house me-1"></i>
                                    @endif
                                    {{ $breadcrumb['name'] }}
                                </a>
                            </li>
                        @else
                            <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('documents.create') }}" class="btn btn-success">
                <i class="bi bi-upload me-1"></i>Upload Document
            </a>
            @if(isset($category))
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i>New Folder
                </button>
            @endif
        </div>
    </div>

    {{-- Category Badges (Top) --}}
    <div class="mb-4">
        <div class="d-flex flex-wrap gap-2">
            @foreach($categories as $cat)
                <a href="{{ route('documents.category', $cat) }}" class="badge {{ isset($category) && $category->id == $cat->id ? 'bg-success' : 'bg-white text-success border' }} py-2 px-3 text-decoration-none">
                    <i class="bi bi-collection me-1"></i>{{ $cat->name }} ({{ $cat->documents_count }})
                </a>
            @endforeach
        </div>
    </div>

    {{-- Search + Filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" method="GET" class="row g-3 align-items-center">
                @if(isset($category))
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                @endif
                @if(isset($currentFolder))
                    <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
                @endif

                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search documents and folders..."
                           value="{{ request('search') }}">
                </div>

                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All File Types</option>
                        <option value="pdf" {{ request('type') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        <option value="word" {{ request('type') == 'word' ? 'selected' : '' }}>Word</option>
                        <option value="excel" {{ request('type') == 'excel' ? 'selected' : '' }}>Excel</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="name" {{ request('sort', 'name') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="type" {{ request('sort') == 'type' ? 'selected' : '' }}>File Type</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    {{-- File Explorer Content --}}
    <div class="card">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="bi bi-grid-3x3-gap me-2"></i>
                @if(isset($currentFolder))
                    Contents of "{{ $currentFolder->name }}"
                @elseif(isset($category))
                    Contents of "{{ $category->name }}"
                @else
                    All Documents and Folders
                @endif
                <span class="badge bg-success ms-2">
                    {{ $content['folders']->count() + $content['documents']->count() }} items
                </span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($content['folders']->count() > 0 || $content['documents']->count() > 0)
                {{-- Folders Grid --}}
                @if($content['folders']->count() > 0)
                    <div class="p-4 border-bottom">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-folder me-2"></i>Folders ({{ $content['folders']->count() }})
                        </h6>
                        <div class="row">
                            @foreach($content['folders'] as $folder)
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                    <div class="card folder-item h-100">
                                        <div class="card-body text-center">
                                            <i class="bi bi-folder-fill text-warning display-6"></i>
                                            <h6 class="card-title mt-2 mb-1">{{ $folder->name }}</h6>
                                            <div class="folder-stats">
                                                <small class="text-muted">
                                                    {{ $folder->documents_count }} documents
                                                </small>
                                                @if($folder->children_count > 0)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $folder->children_count }} subfolders
                                                    </small>
                                                @endif
                                            </div>
                                            @if($folder->description)
                                                <p class="card-text small mt-2">{{ Str::limit($folder->description, 50) }}</p>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent text-center">
                                            <a href="{{ isset($category)
                                                ? route('documents.category', ['category' => $category, 'folder_id' => $folder->id])
                                                : route('documents.list', ['folder_id' => $folder->id]) }}"
                                               class="btn btn-outline-success btn-sm w-100">
                                                <i class="bi bi-folder2-open me-1"></i>Open
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Documents Grid --}}
                @if($content['documents']->count() > 0)
                    <div class="p-4">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-files me-2"></i>Documents ({{ $content['documents']->count() }})
                        </h6>
                        <div class="row">
                            @foreach($content['documents'] as $document)
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                                    <div class="card document-item h-100">
                                        <div class="card-body text-center">
                                            @php
                                                $extension = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
                                                $icon = 'bi-file-earmark';
                                                $color = 'text-primary';

                                                if ($extension === 'pdf') {
                                                    $icon = 'bi-file-earmark-pdf';
                                                    $color = 'text-danger';
                                                } elseif (in_array($extension, ['doc', 'docx'])) {
                                                    $icon = 'bi-file-earmark-word';
                                                    $color = 'text-primary';
                                                } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                                    $icon = 'bi-file-earmark-excel';
                                                    $color = 'text-success';
                                                } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                                    $icon = 'bi-file-earmark-ppt';
                                                    $color = 'text-warning';
                                                }
                                            @endphp
                                            <i class="bi {{ $icon }} {{ $color }} display-6"></i>
                                            <h6 class="card-title mt-2 mb-1">{{ $document->title }}</h6>
                                            <div class="document-meta">
                                                <small class="text-muted text-uppercase">
                                                    {{ $extension }}
                                                </small>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $document->created_at->format('M j, Y') }}
                                                </small>
                                            </div>
                                            @if($document->description)
                                                <p class="card-text small mt-2">{{ Str::limit($document->description, 50) }}</p>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group w-100">
                                                <a href="{{ route('documents.show', $document) }}"
                                                   class="btn btn-outline-success btn-sm" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('documents.download', $document) }}"
                                                   class="btn btn-outline-primary btn-sm" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <a href="{{ route('documents.edit', $document) }}"
                                                   class="btn btn-outline-secondary btn-sm" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-5">
                    <i class="bi bi-folder-x display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">This folder is empty</h4>
                    <p class="text-muted mb-4">
                        @if(isset($category))
                            No documents or folders found in {{ $category->name }}.
                        @else
                            No documents or folders found.
                        @endif
                    </p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success me-2">
                        <i class="bi bi-upload me-1"></i>Upload Document
                    </a>
                    @if(isset($category))
                        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                            <i class="bi bi-folder-plus me-1"></i>Create Folder
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>

<!-- Create Folder Modal -->
@if(isset($category))
<div class="modal fade" id="createFolderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-folder-plus me-2"></i>Create New Folder
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('folders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                    <input type="hidden" name="parent_id" value="{{ $currentFolder->id ?? '' }}">

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

<style>
.folder-item, .document-item {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}
.folder-item:hover, .document-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}
.card {
    border-radius: 0.5rem;
}
.folder-stats, .document-meta {
    font-size: 0.8rem;
}
</style>
@endsection
