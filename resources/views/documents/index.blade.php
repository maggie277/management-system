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
            <h1 class="h4 mb-1">Document Management System</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documents.create') }}" class="btn btn-success">Upload Document</a>
            <a href="{{ route('documents.list') }}" class="btn btn-outline-success">All Documents</a>
        </div>
    </div>

    {{-- Document Stats / Category Cards --}}
    <div class="row mb-4">
        @php $categories = \App\Models\Category::all(); @endphp
        @foreach($categories as $cat)
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('documents.category', $cat) }}" class="text-decoration-none">
                    <div class="card stat-card h-100 shadow-sm text-center p-3">
                        <h6 class="text-success mb-2">{{ $cat->name }}</h6>
                        <h3 class="mb-0">{{ $cat->documents()->count() }}</h3>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Recent Documents --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Recent Documents</h5>
        </div>
        <div class="card-body">
            @if($latestDocs->count() > 0)
                <div class="list-group">
                    @foreach($latestDocs as $document)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $document->title }}</h6>
                                <small class="text-muted">
                                    @if($document->category_id && $document->documentCategory)
                                        <span class="badge bg-success">{{ $document->documentCategory->name }}</span> •
                                    @endif
                                    Uploaded by {{ $document->user->name ?? 'N/A' }} •
                                    {{ $document->created_at->format('M j, Y') }}
                                </small>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('documents.show', $document) }}" class="btn btn-sm btn-outline-success">View</a>
                                <a href="{{ route('documents.edit', $document) }}" class="btn btn-sm btn-success">Edit</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <h4 class="text-muted">No documents yet</h4>
                    <p class="text-muted">Upload your first document to get started</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-success">Upload Document</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-card {
    border-radius: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
    background-color: #ffffff;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 128, 0, 0.15);
}
.card {
    border-radius: 0.5rem;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 128, 0, 0.1);
}
</style>
@endsection
