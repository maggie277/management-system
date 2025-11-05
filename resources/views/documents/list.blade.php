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
                    <i class="bi bi-collection-fill text-success me-2"></i>{{ $category->name }} Documents
                @else
                    <i class="bi bi-files me-2"></i>All Documents
                @endif
            </h1>
            <p class="text-muted mb-0">
                @if(isset($currentFolder))
                    {{ $currentFolder->description ?: 'Browse documents in this folder' }}
                    <span class="badge bg-success ms-2">{{ $category->name }} Category</span>
                @elseif(isset($category))
                    {{ $category->description }}
                @else
                    Browse and manage all uploaded documents
                @endif
            </p>

            {{-- Breadcrumbs --}}
            @if(isset($currentFolder) && isset($category))
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('documents.list') }}" class="text-success text-decoration-none">
                                <i class="bi bi-house me-1"></i>Root
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('documents.category', $category) }}" class="text-success text-decoration-none">
                                {{ $category->name }}
                            </a>
                        </li>
                        @php
                            $breadcrumbs = [];
                            $folder = $currentFolder;
                            while($folder) {
                                $breadcrumbs[] = $folder;
                                $folder = $folder->parent;
                            }
                            $breadcrumbs = array_reverse($breadcrumbs);
                        @endphp
                        @foreach($breadcrumbs as $crumb)
                            @if(!$loop->last)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('documents.category', ['category' => $category, 'folder_id' => $crumb->id]) }}" class="text-success text-decoration-none">
                                        {{ $crumb->name }}
                                    </a>
                                </li>
                            @else
                                <li class="breadcrumb-item active">{{ $crumb->name }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @elseif(isset($category))
                <nav aria-label="breadcrumb" class="mt-2">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('documents.list') }}" class="text-success text-decoration-none">
                                <i class="bi bi-house me-1"></i>Root
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ $category->name }}</li>
                    </ol>
                </nav>
            @endif
        </div>
        <div>
            <a href="{{ route('documents.create') }}" class="btn btn-success">
                <i class="bi bi-upload me-1"></i>Upload Document
            </a>
            @if(isset($category))
                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#createFolderModal">
                    <i class="bi bi-folder-plus me-1"></i>Create Folder
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
                    <input type="text" name="search" class="form-control" placeholder="Search documents..."
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

                @if(isset($category) && $folders->count() > 0)
                <div class="col-md-3">
                    <select name="folder_id" class="form-select">
                        <option value="">All Folders</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" {{ request('folder_id') == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">Apply Filters</button>
                    <a href="{{ isset($category) ? route('documents.category', $category) : route('documents.list') }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Subfolders Section -- Show in category view --}}
    @if(isset($category) && $subfolders->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="bi bi-folder-symlink me-2"></i>Folders in {{ $category->name }}
                </h6>
                <span class="badge bg-success">{{ $subfolders->count() }} folders</span>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($subfolders as $folder)
                        <div class="col-md-3 mb-3">
                            <div class="card folder-card h-100 text-center">
                                <div class="card-body">
                                    <i class="bi bi-folder-fill text-warning display-6"></i>
                                    <h6 class="card-title mt-2">{{ $folder->name }}</h6>
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

    {{-- Documents Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                @if(isset($currentFolder))
                    <i class="bi bi-file-earmark-text me-2"></i>Documents in "{{ $currentFolder->name }}"
                @elseif(isset($category))
                    <i class="bi bi-file-earmark-text me-2"></i>Documents in {{ $category->name }}
                @else
                    <i class="bi bi-file-earmark-text me-2"></i>All Documents
                @endif
                <span class="badge bg-success ms-2">{{ $documents->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($documents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Folder</th>
                                <th>Type</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $document->title }}</strong>
                                                @if($document->description)
                                                    <br><small class="text-muted">{{ Str::limit($document->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($document->category_id && $document->documentCategory)
                                            <span class="badge bg-success text-white">
                                                <i class="bi bi-collection me-1"></i>{{ $document->documentCategory->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-white text-success border">Uncategorized</span>
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
                                        <span class="badge bg-secondary">
                                            {{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}
                                        </span>
                                    </td>
                                    <td>{{ $document->user->name ?? 'N/A' }}</td>
                                    <td>{{ $document->created_at->format('M j, Y') }}</td>
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
                    <i class="bi bi-folder-x display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">
                        @if(isset($currentFolder))
                            No documents found in this folder
                        @elseif(isset($category))
                            No documents found in {{ $category->name }} category
                        @else
                            No documents found
                        @endif
                    </h4>
                    <p class="text-muted mb-3">
                        @if(isset($category) && $subfolders->count() == 0)
                            This category has no folders or documents yet.
                        @endif
                    </p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success">
                        <i class="bi bi-upload me-1"></i>Upload Document
                    </a>
                    @if(isset($category))
                        <button type="button" class="btn btn-outline-success ms-2" data-bs-toggle="modal" data-bs-target="#createFolderModal">
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
                    <div class="mb-3">
                        <label class="form-label">Parent Folder</label>
                        <select name="parent_id" class="form-select">
                            <option value="">No Parent (Top Level)</option>
                            @foreach($folders->where('category_id', $category->id) as $parentFolder)
                                <option value="{{ $parentFolder->id }}">{{ $parentFolder->name }}</option>
                            @endforeach
                        </select>
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
.folder-card {
    transition: transform 0.2s;
    border: 1px solid #e9ecef;
}
.folder-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.breadcrumb {
    background-color: transparent;
    padding: 0;
    margin-bottom: 0;
}
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
.card {
    border-radius: 0.5rem;
}
</style>
@endsection
