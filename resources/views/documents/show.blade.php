@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-1">Document Details</h1>
            <p class="text-muted mb-0">View document information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documents.list') }}" class="btn btn-outline-success">Back to Documents</a>
            <a href="{{ route('documents.edit', $document) }}" class="btn btn-success">Edit Document</a>
        </div>
    </div>

    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            {{-- Document Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Document Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th class="text-muted" width="40%">Title:</th>
                                    <td><strong>{{ $document->title }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Category:</th>
                                    <td>
                                        @if($document->documentCategory)
                                            <span class="badge bg-success">{{ $document->documentCategory->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Uncategorized</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-muted">File Type:</th>
                                    <td>{{ strtoupper(pathinfo($document->file_path, PATHINFO_EXTENSION)) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th class="text-muted" width="40%">Uploaded By:</th>
                                    <td>{{ $document->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Upload Date:</th>
                                    <td>{{ $document->created_at->format('F j, Y g:i A') }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Last Updated:</th>
                                    <td>{{ $document->updated_at->format('F j, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($document->description)
                    <div class="mt-4">
                        <h6 class="text-muted">Description</h6>
                        <p class="border-start border-3 border-success ps-3 py-2 bg-light rounded">
                            {{ $document->description }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
</style>
@endsection
