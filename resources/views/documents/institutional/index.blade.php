@extends('layouts.app')

@section('content')
<style>
:root {
    --primary-green: #198754;
    --light-green: #f3fef6;
    --hover-green: #157347;
    --card-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    --card-shadow-hover: 0 10px 20px rgba(25, 135, 84, 0.2);
}

.card-header {
    border-bottom: none;
    background: linear-gradient(to right, #ffffff, var(--light-green));
}

.hover-brighten {
    transition: all 0.3s ease;
}

.hover-brighten:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 6px rgba(25, 135, 84, 0.3);
}

.document-card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f8fef9);
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
}

.document-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--card-shadow-hover);
    background: linear-gradient(145deg, #e9fff0, #ffffff);
}

.document-item {
    border-left: 3px solid var(--primary-green);
    transition: all 0.2s ease;
}

.document-item:hover {
    background-color: rgba(25, 135, 84, 0.05);
    transform: translateX(5px);
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

.empty-state {
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.badge-success {
    background-color: var(--primary-green);
}

/* List layout styles */
.document-list-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border-radius: 0.75rem;
    background: white;
    border: 1px solid rgba(25, 135, 84, 0.1);
    transition: all 0.3s ease;
}

.document-list-item:hover {
    background: var(--light-green);
    transform: translateX(5px);
    box-shadow: var(--card-shadow);
}

.document-icon {
    font-size: 1.5rem;
    color: var(--primary-green);
    margin-right: 1rem;
    flex-shrink: 0;
}

.document-content {
    flex: 1;
    min-width: 0;
}

.document-actions {
    flex-shrink: 0;
    margin-left: 1rem;
}

.document-meta {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}

.meta-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6c757d;
}

.meta-item i {
    margin-right: 0.25rem;
}

.badge-status {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Responsive design */
@media (max-width: 768px) {
    .document-list-item {
        flex-direction: column;
        align-items: flex-start;
    }

    .document-actions {
        margin-left: 0;
        margin-top: 1rem;
        width: 100%;
        display: flex;
        gap: 0.5rem;
    }

    .document-meta {
        flex-direction: column;
        gap: 0.25rem;
    }

    .document-actions .btn {
        flex: 1;
    }
}

/* Search and filter styles */
.search-filter-section {
    background: linear-gradient(to right, #ffffff, var(--light-green));
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
</style>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h1 class="h4 mb-0 fw-bold text-success">
                            <i class="bi bi-folder-fill me-2"></i>Institutional Documents
                        </h1>
                        <p class="text-muted mb-0 mt-1">Access important institutional documents, policies, and resources</p>
                    </div>
                    @if(auth()->user()->isManagement())
                        <a href="{{ route('documents.institutional.create') }}" class="btn btn-success hover-brighten">
                            <i class="bi bi-upload me-1"></i>Upload Document
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="search-filter-section">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search documents..." id="searchInput">
                    <button class="btn btn-outline-success hover-brighten" type="button" id="searchButton">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="row">
        @forelse($documents as $category => $categoryDocuments)
            <div class="col-12 mb-4 document-category" data-category="{{ $category }}">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="card-title mb-0 fw-bold text-success">
                            <i class="bi bi-folder me-2"></i>{{ $category }}
                        </h5>
                        <span class="badge bg-success rounded-pill">{{ $categoryDocuments->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($categoryDocuments as $document)
                                <div class="list-group-item px-0 document-item document-list-item"
                                     data-name="{{ strtolower($document->name) }}"
                                     data-description="{{ strtolower($document->description ?? '') }}"
                                     data-category="{{ $document->category }}">
                                    <div class="document-icon">
                                        <i class="bi {{ $document->getFileIcon() }}"></i>
                                    </div>
                                    <div class="document-content">
                                        <h6 class="fw-bold mb-1 text-success">{{ $document->name }}</h6>
                                        <p class="text-muted small mb-2">
                                            {{ $document->description ?? 'No description provided' }}
                                        </p>
                                        <div class="document-meta">
                                            <span class="meta-item">
                                                <i class="bi bi-file-earmark"></i>{{ $document->getFormattedSize() }}
                                            </span>
                                            <span class="meta-item">
                                                <i class="bi bi-download"></i>{{ $document->download_count }} downloads
                                            </span>
                                            <span class="meta-item">
                                                <i class="bi bi-person"></i>{{ $document->uploader->name }}
                                            </span>
                                            <span class="meta-item">
                                                <i class="bi bi-calendar"></i>{{ $document->created_at->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="document-actions">
                                        <a href="{{ route('documents.institutional.show', $document->id) }}"
                                           class="btn btn-outline-success btn-sm hover-brighten">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>
                                        @if(auth()->user()->isManagement())
                                            <form action="{{ route('documents.institutional.destroy', $document->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm hover-brighten"
                                                        onclick="return confirm('Are you sure you want to delete this document? This action cannot be undone.')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5 empty-state">
                        <i class="bi bi-folder-x"></i>
                        <h4 class="text-muted">No documents available</h4>
                        <p class="text-muted">There are no institutional documents uploaded yet.</p>
                        @if(auth()->user()->isManagement())
                            <a href="{{ route('documents.institutional.create') }}" class="btn btn-success mt-2 hover-brighten">
                                <i class="bi bi-upload me-1"></i>Upload First Document
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- No Results Message (Hidden by default) -->
    <div id="noResults" class="row" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5 empty-state">
                    <i class="bi bi-search"></i>
                    <h4 class="text-muted">No documents found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchButton = document.getElementById('searchButton');
    const noResults = document.getElementById('noResults');

    function filterDocuments() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;

        let visibleDocuments = 0;
        let visibleCategories = 0;

        // Show all categories and documents first
        document.querySelectorAll('.document-category').forEach(category => {
            category.style.display = 'block';
        });

        document.querySelectorAll('.document-item').forEach(item => {
            item.style.display = 'flex';
        });

        // Apply search filter
        if (searchTerm) {
            document.querySelectorAll('.document-item').forEach(item => {
                const name = item.getAttribute('data-name');
                const description = item.getAttribute('data-description');

                if (name.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'flex';
                    visibleDocuments++;
                } else {
                    item.style.display = 'none';
                }
            });
        } else {
            document.querySelectorAll('.document-item').forEach(item => {
                item.style.display = 'flex';
                visibleDocuments++;
            });
        }

        // Apply category filter
        if (selectedCategory) {
            document.querySelectorAll('.document-category').forEach(category => {
                const categoryName = category.getAttribute('data-category');

                if (categoryName === selectedCategory) {
                    category.style.display = 'block';

                    // Count visible documents in this category
                    const visibleInCategory = Array.from(category.querySelectorAll('.document-item'))
                        .filter(item => item.style.display !== 'none').length;

                    if (visibleInCategory > 0) {
                        visibleCategories++;
                    } else {
                        category.style.display = 'none';
                    }
                } else {
                    category.style.display = 'none';
                }
            });
        } else {
            // Count visible categories when no category filter
            document.querySelectorAll('.document-category').forEach(category => {
                const visibleInCategory = Array.from(category.querySelectorAll('.document-item'))
                    .filter(item => item.style.display !== 'none').length;

                if (visibleInCategory > 0) {
                    category.style.display = 'block';
                    visibleCategories++;
                } else {
                    category.style.display = 'none';
                }
            });
        }

        // Show/hide no results message
        if (visibleDocuments === 0) {
            noResults.style.display = 'block';
            document.querySelectorAll('.document-category').forEach(category => {
                category.style.display = 'none';
            });
        } else {
            noResults.style.display = 'none';
        }
    }

    // Event listeners
    searchInput.addEventListener('input', filterDocuments);
    searchButton.addEventListener('click', filterDocuments);
    categoryFilter.addEventListener('change', filterDocuments);

    // Clear search when escape key is pressed
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterDocuments();
        }
    });

    // Focus search input on page load
    searchInput.focus();
});
</script>
@endsection
