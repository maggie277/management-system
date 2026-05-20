@extends('layouts.app')

@section('content')
<style>
:root {
    --primary-green: #198754;
    --light-green: #f3fef6;
    --hover-green: #157347;
    --card-shadow: 0 3px 10px rgba(25, 135, 84, 0.1);
    --card-shadow-hover: 0 5px 15px rgba(25, 135, 84, 0.15);
}

.card {
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
    background: #ffffff;
}

.card-header {
    border-bottom: 1px solid #e9ecef;
    background: #ffffff;
}

.hover-brighten {
    transition: all 0.2s ease;
}

.hover-brighten:hover {
    transform: translateY(-1px);
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

.document-list-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.2s ease;
}

.document-list-item:hover {
    background-color: #f8f9fa;
}

.document-content {
    flex: 1;
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
    font-size: 0.8rem;
    color: #6c757d;
}

.meta-item {
    display: inline-flex;
    align-items: center;
}

.meta-item i {
    margin-right: 0.25rem;
}

.category-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    background: #e9ecef;
    color: #495057;
}

@media (max-width: 768px) {
    .document-list-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .document-actions {
        margin-left: 0;
        margin-top: 0.75rem;
    }
}

.search-filter-section {
    background: #f8f9fa;
    border-radius: 0.75rem;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e9ecef;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #adb5bd;
}

.type-section-header {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary-green);
    padding: 0.75rem 1.25rem;
    background: #f8f9fa;
    border-bottom: 2px solid var(--primary-green);
}
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <div>
                        <h1 class="h4 mb-0 fw-bold text-success">Institutional Documents</h1>
                        <p class="text-muted mb-0 mt-1">Access policies, manuals, templates, reports, and forms</p>
                    </div>
                    @if(auth()->user()->isManagement())
                        <a href="{{ route('documents.institutional.create') }}" class="btn btn-success hover-brighten">
                            Upload Document
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="search-filter-section">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" placeholder="Search documents..." id="searchInput">
            </div>
            <div class="col-md-6">
                <select class="form-select" id="categoryFilter">
                    <option value="">All Categories</option>
                    <option value="Policies/Manual">Policies/Manual</option>
                    <option value="Templates">Templates</option>
                    <option value="Research Reports">Research Reports</option>
                    <option value="Program Reports">Program Reports</option>
                    <option value="Leave Form">Leave Form</option>
                    <option value="Retirement Form">Retirement Form</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents by Category -->
    @php
        $categoryOrder = ['Policies/Manual', 'Templates', 'Research Reports', 'Program Reports', 'Leave Form', 'Retirement Form'];
        $sortedDocuments = [];
        foreach ($categoryOrder as $cat) {
            if (isset($documents[$cat])) {
                $sortedDocuments[$cat] = $documents[$cat];
            }
        }
        // Add any remaining categories
        foreach ($documents as $cat => $docs) {
            if (!in_array($cat, $categoryOrder)) {
                $sortedDocuments[$cat] = $docs;
            }
        }
    @endphp

    <div class="row">
        @forelse($sortedDocuments as $category => $categoryDocuments)
            <div class="col-12 mb-4 document-category" data-category="{{ $category }}">
                <div class="card border-0 shadow-sm">
                    <div class="type-section-header">
                        {{ $category }}
                        <span class="badge bg-success ms-2">{{ $categoryDocuments->count() }}</span>
                    </div>
                    <div class="card-body p-0">
                        @foreach($categoryDocuments as $document)
                            <div class="document-list-item"
                                 data-name="{{ strtolower($document->name) }}"
                                 data-description="{{ strtolower($document->description ?? '') }}"
                                 data-category="{{ $document->category }}">
                                <div class="document-content">
                                    <h6 class="fw-bold mb-1">{{ $document->name }}</h6>
                                    @if($document->description)
                                        <p class="text-muted small mb-2">{{ $document->description }}</p>
                                    @endif
                                    <div class="document-meta">
                                        <span class="meta-item">File: {{ $document->getFormattedSize() }}</span>
                                        <span class="meta-item">Downloads: {{ $document->download_count }}</span>
                                        <span class="meta-item">Uploaded by: {{ $document->uploader->name }}</span>
                                        <span class="meta-item">Date: {{ $document->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="document-actions">
                                    <a href="{{ route('documents.institutional.show', $document->id) }}"
                                       class="btn btn-outline-success btn-sm">
                                        Download
                                    </a>
                                    @if(auth()->user()->isManagement())
                                        <form action="{{ route('documents.institutional.destroy', $document->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this document?')">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="text-muted">No documents available</h4>
                        <p class="text-muted">There are no institutional documents uploaded yet.</p>
                        @if(auth()->user()->isManagement())
                            <a href="{{ route('documents.institutional.create') }}" class="btn btn-success mt-2">Upload First Document</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div id="noResults" class="row" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
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
    const noResults = document.getElementById('noResults');

    function filterDocuments() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const selectedCategory = categoryFilter.value;

        let visibleDocuments = 0;

        document.querySelectorAll('.document-category').forEach(category => {
            category.style.display = 'block';
        });

        document.querySelectorAll('.document-list-item').forEach(item => {
            item.style.display = 'flex';
        });

        if (searchTerm) {
            document.querySelectorAll('.document-list-item').forEach(item => {
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
            document.querySelectorAll('.document-list-item').forEach(item => {
                item.style.display = 'flex';
                visibleDocuments++;
            });
        }

        if (selectedCategory) {
            document.querySelectorAll('.document-category').forEach(category => {
                const categoryName = category.getAttribute('data-category');
                if (categoryName === selectedCategory) {
                    const visibleInCategory = Array.from(category.querySelectorAll('.document-list-item'))
                        .filter(item => item.style.display !== 'none').length;
                    if (visibleInCategory > 0) {
                        category.style.display = 'block';
                        visibleDocuments = visibleInCategory;
                    } else {
                        category.style.display = 'none';
                        visibleDocuments = 0;
                    }
                } else {
                    category.style.display = 'none';
                }
            });
        } else {
            document.querySelectorAll('.document-category').forEach(category => {
                const visibleInCategory = Array.from(category.querySelectorAll('.document-list-item'))
                    .filter(item => item.style.display !== 'none').length;
                if (visibleInCategory === 0) {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });
        }

        noResults.style.display = visibleDocuments === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterDocuments);
    categoryFilter.addEventListener('change', filterDocuments);

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            filterDocuments();
        }
    });
});
</script>
@endsection
