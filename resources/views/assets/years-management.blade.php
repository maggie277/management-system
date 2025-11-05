@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-success">
                        <i class="bi bi-columns-gap me-2"></i>Manage Year Columns
                    </h5>
                    <div>
                        <a href="{{ route('asset-register') }}" class="btn btn-success">
                            <i class="bi bi-arrow-left me-2"></i>Back to Assets
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Success / Error Alerts --}}
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

                    {{-- Add Year Form --}}
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0 fw-semibold">Add New Year Column</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('depreciation-years.add') }}" method="POST" class="row g-3">
                                @csrf
                                <div class="col-md-3">
                                    <label for="year" class="form-label fw-semibold text-success">Year</label>
                                    <input type="number" class="form-control border-success" id="year" name="year"
                                           min="2011" max="2100" value="{{ date('Y') + 1 }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="position" class="form-label fw-semibold text-success">Insert After Position</label>
                                    <select class="form-select border-success" id="position" name="position">
                                        <option value="">Add at end</option>
                                        @foreach($years as $year)
                                            <option value="{{ $year->position + 1 }}">
                                                After {{ $year->year }} (Position {{ $year->position }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5 d-flex align-items-end">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle me-2"></i>Add Year Column
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Current Year Columns --}}
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0 fw-semibold">Current Year Columns ({{ $years->count() }})</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-success text-success">
                                        <tr>
                                            <th>Position</th>
                                            <th>Year</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($years as $year)
                                            <tr>
                                                <td class="fw-bold">{{ $year->position }}</td>
                                                <td class="fw-bold">{{ $year->year }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $year->is_active ? 'success' : 'secondary' }}">
                                                        {{ $year->is_active ? 'Active' : 'Hidden' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <form action="{{ route('depreciation-years.toggle', $year->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @if($year->is_active)
                                                                <button type="submit" class="btn btn-outline-success text-success bg-white border-success">
                                                                    Hide
                                                                </button>
                                                            @else
                                                                <button type="submit" class="btn btn-success text-white">
                                                                    Show
                                                                </button>
                                                            @endif
                                                        </form>

                                                        @if($year->year > date('Y'))
                                                        <form action="{{ route('depreciation-years.delete', $year->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger bg-white"
                                                                    onclick="return confirm('Delete year {{ $year->year }} column?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div> {{-- End Card Body --}}
            </div>
        </div>
    </div>
</div>
@endsection
