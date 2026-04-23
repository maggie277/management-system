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

.card {
    border-radius: 1rem;
    border: 1px solid rgba(25, 135, 84, 0.15);
    background: linear-gradient(145deg, #ffffff, #f8fef9);
    box-shadow: var(--card-shadow);
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

.form-control:focus, .form-select:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}

.file-upload-area {
    border: 2px dashed rgba(25, 135, 84, 0.3);
    border-radius: 0.75rem;
    padding: 2rem;
    text-align: center;
    background: rgba(25, 135, 84, 0.05);
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: var(--primary-green);
    background: rgba(25, 135, 84, 0.1);
}

.file-upload-area.dragover {
    border-color: var(--primary-green);
    background: rgba(25, 135, 84, 0.15);
    transform: scale(1.02);
}

.file-info {
    background: var(--light-green);
    border: 1px solid rgba(25, 135, 84, 0.2);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-top: 1rem;
    display: none;
}

.file-info.show {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.upload-icon {
    font-size: 3rem;
    color: var(--primary-green);
    margin-bottom: 1rem;
}

.required-field::after {
    content: " *";
    color: #dc3545;
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
                            <i class="bi bi-upload me-2"></i>Upload Institutional Document
                        </h1>
                    </div>
                    <a href="{{ route('documents.institutional.index') }}" class="btn btn-outline-success hover-brighten">
                        <i class="bi bi-arrow-left me-1"></i>Back to Documents
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-success">
                        <i class="bi bi-file-earmark-plus me-2"></i>Document Details
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('documents.institutional.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label fw-bold required-field">Document Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required
                                           placeholder="Enter document name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="category" class="form-label fw-bold required-field">Category</label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Policies" {{ old('category') == 'Policies' ? 'selected' : '' }}>Policies</option>
                                        <option value="Procedures" {{ old('category') == 'Procedures' ? 'selected' : '' }}>Procedures</option>
                                        <option value="Templates" {{ old('category') == 'Templates' ? 'selected' : '' }}>Templates</option>
                                        <option value="Reports" {{ old('category') == 'Reports' ? 'selected' : '' }}>Reports</option>
                                        <option value="Guidelines" {{ old('category') == 'Guidelines' ? 'selected' : '' }}>Guidelines</option>
                                        <option value="Forms" {{ old('category') == 'Forms' ? 'selected' : '' }}>Forms</option>
                                        <option value="Manuals" {{ old('category') == 'Manuals' ? 'selected' : '' }}>Manuals</option>
                                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Brief description of the document">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- File Upload Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold required-field">Document File</label>

                            <!-- Drag and Drop Area -->
                            <div class="file-upload-area" id="fileUploadArea">
                                <div class="upload-icon">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <h5 class="text-success mb-2">Drop your file here</h5>
                                <p class="text-muted mb-3">or click to browse</p>
                                <input type="file" class="d-none" id="document" name="document"
                                       accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.jpg,.jpeg,.png" required>
                                <button type="button" class="btn btn-outline-success hover-brighten" onclick="document.getElementById('document').click()">
                                    <i class="bi bi-folder2-open me-1"></i>Choose File
                                </button>
                                <p class="small text-muted mt-2 mb-0">
                                    Accepted formats: PDF, Word, Excel, PowerPoint, Text, ZIP, Images<br>
                                    Max size: 10MB
                                </p>
                            </div>

                            <!-- File Info Display -->
                            <div class="file-info" id="fileInfo">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-earmark-text text-success fs-4 me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold" id="fileName">No file selected</h6>
                                        <p class="mb-0 text-muted small" id="fileSize">-</p>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm hover-brighten" onclick="clearFile()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>

                            @error('document')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end border-top pt-4">
                            <a href="{{ route('documents.institutional.index') }}" class="btn btn-outline-secondary me-md-2 hover-brighten">Cancel</a>
                            <button type="submit" class="btn btn-success hover-brighten" id="submitBtn">
                                <i class="bi bi-upload me-1"></i>Upload Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('document');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadForm = document.getElementById('uploadForm');
    const submitBtn = document.getElementById('submitBtn');

    // File input change handler
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        fileUploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        fileUploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        fileUploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        fileUploadArea.classList.add('dragover');
    }

    function unhighlight() {
        fileUploadArea.classList.remove('dragover');
    }

    fileUploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFileSelect();
    }

    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            // Validate file size (10MB)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert('File size must be less than 10MB', 'danger');
                clearFile();
                return;
            }

            // Validate file type
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'text/plain',
                'application/zip',
                'image/jpeg',
                'image/jpg',
                'image/png'
            ];

            if (!allowedTypes.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip|jpg|jpeg|png)$/i)) {
                showAlert('File type not allowed. Please upload a valid document type.', 'danger');
                clearFile();
                return;
            }

            // Update file info display
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            fileName.textContent = file.name;
            fileSize.textContent = `${fileSizeMB} MB • ${file.type.split('/')[1].toUpperCase()}`;
            fileInfo.classList.add('show');

            showAlert('File selected successfully!', 'success');
        }
    }

    function clearFile() {
        fileInput.value = '';
        fileInfo.classList.remove('show');
        fileName.textContent = 'No file selected';
        fileSize.textContent = '-';
    }

    function showAlert(message, type) {
        // Remove existing alerts
        const existingAlert = document.querySelector('.alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Insert after the header card
        const headerCard = document.querySelector('.card');
        headerCard.parentNode.insertBefore(alertDiv, headerCard.nextSibling);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Form submission handler
    uploadForm.addEventListener('submit', function(e) {
        const file = fileInput.files[0];
        if (!file) {
            e.preventDefault();
            showAlert('Please select a file to upload', 'danger');
            return;
        }

        // Show loading state
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Uploading...';
        submitBtn.disabled = true;
    });

    // Click on upload area to trigger file input
    fileUploadArea.addEventListener('click', function() {
        fileInput.click();
    });
});
</script>
@endsection
