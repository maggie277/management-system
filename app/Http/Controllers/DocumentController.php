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
     * Display the documents dashboard (index page)
     */
    public function index()
    {
        $categories = Category::all();
        return view('documents.index', compact('categories'));
    }

    /**
     * Display a list of documents with folder support
     */
    public function list(Request $request)
    {
        // Get categories with document counts
        $categories = Category::withCount('documents')->get();

        $totalDocuments = Document::count();

        $currentCategory = null;
        $currentFolder   = null;
        $subfolders      = collect();
        $parentFolders   = collect();

        // ── Resolve category ──────────────────────────────────────────────────
        if ($request->filled('category_id')) {
            $currentCategory = Category::find($request->category_id);
        } elseif ($request->filled('category')) {
            $currentCategory = Category::find($request->category);
        }

        // ── Resolve folder ────────────────────────────────────────────────────
        if ($request->filled('folder_id')) {
            $currentFolder = Folder::find($request->folder_id);
            // Safety: reject folder that doesn't belong to the current category
            if ($currentFolder && $currentCategory
                && $currentFolder->category_id !== $currentCategory->id) {
                $currentFolder = null;
            }
        }

        // ── Breadcrumb parents ────────────────────────────────────────────────
        if ($currentFolder) {
            $parentFolders = $this->getParentFolders($currentFolder);
        }

        // ── Subfolders ────────────────────────────────────────────────────────
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

        // ── Documents query ───────────────────────────────────────────────────
        $query = Document::with(['user', 'folder', 'documentCategory', 'employee'])
            ->latest();

        $isSearching = $request->filled('search');

        if ($isSearching) {
            // Global search: ignore category / folder filters so budget docs etc. are found
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title',       'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('file_name',   'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
            // Still scope to category when searching inside one
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }
        } else {
            // Normal navigation: scope to category + folder
            if ($currentCategory) {
                $query->where('category_id', $currentCategory->id);
            }

            if ($currentFolder) {
                // Inside a specific folder
                $query->where('folder_id', $currentFolder->id);
            } elseif ($currentCategory) {
                // Category root: show documents that have NO folder assigned
                // (documents with folder_id are shown when you open that folder)
                $query->whereNull('folder_id');
            }
            // No category selected → show everything (All Documents view)
        }

        $documents = $query->paginate(10);

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
     * Display documents filtered by category (named route helper)
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
            'folder_id'   => $folderId,
        ]));
    }

    // ── Private navigation helpers ────────────────────────────────────────────

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

    private function getBackUrl($currentCategory, $currentFolder)
    {
        if ($currentFolder) {
            if ($currentFolder->parent_id) {
                return route('documents.list', [
                    'category_id' => $currentCategory->id,
                    'folder_id'   => $currentFolder->parent_id,
                ]);
            }
            return route('documents.category', $currentCategory);
        }

        if ($currentCategory) {
            return route('documents.category', $currentCategory);
        }

        return route('documents.list');
    }

    // ── CRUD ──────────────────────────────────────────────────────────────────

    /**
     * Show the form for creating a new document.
     */
    public function create(Request $request)
    {
        $categories      = Category::all();
        $currentCategory = null;
        $currentFolder   = null;
        $backUrl         = route('documents.list');

        if ($request->filled('category_id')) {
            $currentCategory = Category::find($request->category_id);
            $backUrl = route('documents.category', $currentCategory);
        }

        if ($request->filled('folder_id')) {
            $currentFolder = Folder::find($request->folder_id);
            if ($currentFolder) {
                $currentCategory = $currentFolder->category;
                $backUrl = route('documents.list', [
                    'category_id' => $currentCategory->id,
                    'folder_id'   => $currentFolder->id,
                ]);
            }
        }

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

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'file'        => 'required|file|max:10240',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ], [
            'file.required'        => 'Please select a file to upload.',
            'file.max'             => 'The file size must not exceed 10MB.',
            'category_id.required' => 'Please select a category.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        }

        try {
            DB::beginTransaction();

            $file     = $request->file('file');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            $filePath = $file->storeAs('documents', $fileName, 'public');

            $documentData = [
                'title'         => $request->title,
                'file_name'     => $file->getClientOriginalName(),
                'file_path'     => $filePath,
                'file_size'     => $file->getSize(),
                'file_type'     => $file->getClientMimeType(),
                'description'   => $request->description ?? null,
                'category_id'   => $request->category_id,
                'folder_id'     => $request->filled('current_folder_id') ? $request->current_folder_id : null,
                'created_by'    => Auth::id(),
                'status'        => 'active',
                'department'    => 'general',
                'document_type' => 'other',
                'version'       => '1.0',
            ];

            $document = Document::create($documentData);

            DB::commit();

            return redirect($this->getRedirectUrl($request, $document))
                ->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Document store error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while uploading the document: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getRedirectUrl(Request $request, Document $document)
    {
        if ($request->filled('current_folder_id')) {
            $folder = Folder::find($request->current_folder_id);
            if ($folder) {
                return route('documents.list', [
                    'category_id' => $folder->category_id,
                    'folder_id'   => $folder->id,
                ]);
            }
        }

        if ($request->filled('category_id')) {
            return route('documents.category', ['category' => $request->category_id]);
        }

        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id'   => $document->folder_id,
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
        $document   = Document::findOrFail($id);
        $categories = Category::all();
        $backUrl    = $this->getEditBackUrl($document);

        return view('documents.edit', compact('document', 'categories', 'backUrl'));
    }

    private function getEditBackUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id'   => $document->folder_id,
            ]);
        }

        if ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        }

        return route('documents.list');
    }

    /**
     * Update the specified document in storage.
     */
    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'file'        => 'nullable|file|max:10240',
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
                'title'       => $request->title,
                'description' => $request->description ?? null,
                'category_id' => $request->category_id,
            ];

            if ($request->hasFile('file')) {
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $file     = $request->file('file');
                $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filePath = $file->storeAs('documents', $fileName, 'public');

                $documentData['file_name'] = $file->getClientOriginalName();
                $documentData['file_path'] = $filePath;
                $documentData['file_size'] = $file->getSize();
                $documentData['file_type'] = $file->getClientMimeType();
            }

            $document->update($documentData);

            DB::commit();

            return redirect($this->getEditRedirectUrl($document))
                ->with('success', 'Document updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while updating the document: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function getEditRedirectUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id'   => $document->folder_id,
            ]);
        }

        if ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        }

        return route('documents.list');
    }

    /**
     * Remove the specified document from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $document    = Document::findOrFail($id);
            $redirectUrl = $this->getDestroyRedirectUrl($document);

            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            DB::commit();

            return redirect($redirectUrl)->with('success', 'Document deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while deleting the document.');
        }
    }

    private function getDestroyRedirectUrl(Document $document)
    {
        if ($document->folder_id) {
            return route('documents.list', [
                'category_id' => $document->category_id,
                'folder_id'   => $document->folder_id,
            ]);
        }

        if ($document->category_id) {
            return route('documents.category', ['category' => $document->category_id]);
        }

        return route('documents.list');
    }

    /**
     * Download the document file.
     */
    public function download($id)
    {
        $document = Document::findOrFail($id);

        // Budget / reference documents have no physical file
        if (!$document->file_path) {
            return redirect()->back()->with('error', 'This document has no downloadable file. View it in the Budget module.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            return redirect()->back()->with('error', 'File not found on disk.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
