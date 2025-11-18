<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Display a list of documents with folder support
     */
    public function list(Request $request)
    {
        // Get categories with document counts
        $categories = Category::withCount('documents')->get();

        // Get total documents count
        $totalDocuments = Document::count();

        // Get current category and folder
        $currentCategory = null;
        $currentFolder = null;
        $subfolders = collect();
        $parentFolders = collect();

        // Handle category and folder navigation
        if ($request->has('category_id')) {
            $currentCategory = Category::find($request->category_id);
        } elseif ($request->has('category')) {
            $currentCategory = Category::find($request->category);
        }

        if ($request->has('folder_id')) {
            $currentFolder = Folder::find($request->folder_id);
            if ($currentFolder && $currentCategory && $currentFolder->category_id !== $currentCategory->id) {
                $currentFolder = null;
            }
        }

        // Get parent folders for breadcrumb
        if ($currentFolder) {
            $parentFolders = $this->getParentFolders($currentFolder);
        }

        // Get subfolders for current location
        if ($currentFolder) {
            $subfolders = Folder::where('parent_id', $currentFolder->id)
                ->withCount(['documents', 'children'])
                ->get();
        } elseif ($currentCategory) {
            $subfolders = Folder::where('category_id', $currentCategory->id)
                ->whereNull('parent_id')
                ->withCount(['documents', 'children'])
                ->get();
        }

        // Build documents query
        $query = Document::with(['user', 'folder', 'documentCategory', 'employee'])
            ->latest();

        // Filter by category if specified
        if ($currentCategory) {
            $query->where('category_id', $currentCategory->id);
        }

        // Filter by folder if specified
        if ($currentFolder) {
            $query->where('folder_id', $currentFolder->id);
        } elseif ($currentCategory && !$request->has('search')) {
            // When in a category but no specific folder, show documents without folders
            $query->whereNull('folder_id');
        }

        // Apply search filter if provided
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(10);

        // Determine back URL for create form
        $backUrl = $this->getBackUrl($currentCategory, $currentFolder);

        return view('documents.list', compact(
            'documents',
            'categories',
            'totalDocuments',
            'currentCategory',
            'currentFolder',
            'subfolders',
            'parentFolders',
            'backUrl'
        ));
    }

    /**
     * Get parent folders for breadcrumb
     */
    private function getParentFolders(Folder $folder)
    {
        $parents = collect();
        $current = $folder;

        while ($current->parent) {
            $parents->prepend($current->parent);
            $current = $current->parent;
        }

        return $parents;
    }

    /**
     * Get folder URL for navigation
     */
    private function getFolderUrl($folder, $currentCategory)
    {
        // Priority: currentCategory > folder's category
        $categoryParam = $currentCategory ?? $folder->category;

        if ($categoryParam) {
            return route('folders.show', [
                'category' => $categoryParam->id ?? $categoryParam,
                'folder' => $folder->id
            ]);
        }

        // Fallback
        return '#';
    }

    /**
     * Get back URL for navigation
     */
    private function getBackUrl($currentCategory, $currentFolder)
    {
        if ($currentFolder) {
            if ($currentFolder->parent_id) {
                // Go back to parent folder
                return route('documents.list', [
                    'category_id' => $currentCategory->id,
                    'folder_id' => $currentFolder->parent_id
                ]);
            } else {
                // Go back to category root
                return route('documents.category', $currentCategory);
            }
        } elseif ($currentCategory) {
            return route('documents.category', $currentCategory);
        } else {
            return route('documents.list');
        }
    }

    /**
     * Show the form for creating a new document.
     */
    public function create(Request $request)
    {
        $categories = Category::all();
        $currentCategory = null;
        $currentFolder = null;
        $backUrl = route('documents.list');

        // Get current category and folder from request
        if ($request->has('category_id')) {
            $currentCategory = Category::find($request->category_id);
            $backUrl = route('documents.category', $currentCategory);
        }

        if ($request->has('folder_id')) {
            $currentFolder = Folder::find($request->folder_id);
            if ($currentFolder) {
                $currentCategory = $currentFolder->category;
                $backUrl = route('documents.list', [
                    'category_id' => $currentCategory->id,
                    'folder_id' => $currentFolder->id
                ]);
            }
        }

        // If no specific category/folder, use first category as default
        if (!$currentCategory && $categories->isNotEmpty()) {
            $currentCategory = $categories->first();
        }

        return view('documents.create', compact(
            'categories',
            'currentCategory',
            'currentFolder',
            'backUrl'
        ));
    }

    /**
     * Store a newly created document in storage.
     */
    public function store(Request $request)
    {
        Log::info('=== DOCUMENT STORE METHOD CALLED ===');
        Log::info('Request data:', $request->except(['file']));

        // Enhanced validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'The file size must not exceed 10MB.',
            'category_id.required' => 'Please select a category.',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            DB::beginTransaction();

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                Log::info('File details:', [
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime' => $file->getClientMimeType(),
                ]);

                // Generate unique filename
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('documents', $fileName, 'public');

                Log::info('File stored at:', ['path' => $filePath]);

                // Prepare document data
                $documentData = [
                    'title' => $request->title,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'description' => $request->description ?? null,
                    'category_id' => $request->category_id,
                    'created_by' => Auth::id(),
                    'status' => 'active',
                    // Set default values for required fields
                    'department' => 'general',
                    'document_type' => 'other',
                    'version' => '1.0',
                ];

                // Set folder_id if provided
                if ($request->has('current_folder_id') && $request->current_folder_id) {
                    $documentData['folder_id'] = $request->current_folder_id;
                }

                Log::info('Final document data:', $documentData);

                // Create the document
                $document = Document::create($documentData);

                Log::info('Document created successfully:', ['document_id' => $document->id]);

                DB::commit();

                // Redirect back to the appropriate location
                $redirectUrl = $this->getRedirectUrl($request, $document);
                return redirect($redirectUrl)
                    ->with('success', 'Document uploaded successfully!');
            } else {
                Log::error('No file uploaded');
                return back()->with('error', 'No file was uploaded. Please select a file.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document store error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'An error occurred while uploading the document: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get redirect URL based on current location
     */
    private function getRedirectUrl(Request $request, Document $document)
    {
        // Priority: current_folder_id > category_id > documents.list
        if ($request->has('current_folder_id') && $request->current_folder_id) {
            $folder = Folder::find($request->current_folder_id);
            if ($folder) {
                return route('documents.list', [
                    'category_id' => $folder->category_id,
                    'folder_id' => $folder->id
                ]);
            }
        }

        if ($request->has('category_id') && $request->category_id) {
            return route('documents.category', ['category' => $request->category_id]);
        }

        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id' => $document->folder_id
            ]);
        }

        if ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        }

        return route('documents.list');
    }

    /**
     * Display the specified document.
     */
    public function show($id)
    {
        $document = Document::with(['user', 'folder', 'documentCategory', 'employee'])
            ->findOrFail($id);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit($id)
    {
        $document = Document::findOrFail($id);
        $categories = Category::all();
        $backUrl = $this->getEditBackUrl($document);

        return view('documents.edit', compact('document', 'categories', 'backUrl'));
    }

    /**
     * Get back URL for edit form
     */
    private function getEditBackUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id' => $document->folder_id
            ]);
        } elseif ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        } else {
            return route('documents.list');
        }
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            DB::beginTransaction();

            $documentData = [
                'title' => $request->title,
                'description' => $request->description ?? null,
                'category_id' => $request->category_id,
            ];

            // Handle file upload if new file is provided
            if ($request->hasFile('file')) {
                // Delete old file
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $file = $request->file('file');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('documents', $fileName, 'public');

                $documentData['file_name'] = $file->getClientOriginalName();
                $documentData['file_path'] = $filePath;
                $documentData['file_size'] = $file->getSize();
                $documentData['file_type'] = $file->getClientMimeType();
            }

            $document->update($documentData);

            DB::commit();

            // Redirect back to appropriate location
            $redirectUrl = $this->getEditRedirectUrl($document);
            return redirect($redirectUrl)
                ->with('success', 'Document updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while updating the document: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get redirect URL for after edit
     */
    private function getEditRedirectUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id' => $document->folder_id
            ]);
        } elseif ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        } else {
            return route('documents.list');
        }
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $document = Document::findOrFail($id);
            $redirectUrl = $this->getDestroyRedirectUrl($document);

            // Delete file from storage
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            DB::commit();

            return redirect($redirectUrl)
                ->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while deleting the document.');
        }
    }

    /**
     * Get redirect URL after destroy
     */
    private function getDestroyRedirectUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id' => $document->folder_id
            ]);
        } elseif ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        } else {
            return route('documents.list');
        }
    }

    /**
     * Download the document file.
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Display documents by category with folder support
     */
    public function byCategory(Category $category, Request $request)
    {
        return $this->list($request->merge(['category' => $category->id]));
    }

    /**
     * Open a specific folder
     */
    public function openFolder(Request $request, $categoryId, $folderId)
    {
        return $this->list($request->merge([
            'category_id' => $categoryId,
            'folder_id' => $folderId
        ]));
    }
}
