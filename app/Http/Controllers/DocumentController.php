<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Category;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Display the documents dashboard.
     */
    public function index()
    {
        $totalDocuments = Document::count();
        $totalPDF = Document::where('file_path', 'like', '%.pdf')->count();
        $totalWord = Document::where('file_path', 'like', '%.doc%')->count();
        $totalExcel = Document::where('file_path', 'like', '%.xls%')->count();
        $latestDocs = Document::with('user')->latest()->take(5)->get();

        return view('documents.index', compact(
            'totalDocuments',
            'totalPDF',
            'totalWord',
            'totalExcel',
            'latestDocs'
        ));
    }

    /**
     * Show upload form.
     */
    public function create()
    {
        $folders = Folder::with('category')->get();
        $categories = Category::all();

        return view('documents.create', compact('folders', 'categories'));
    }

    /**
     * List all uploaded documents with search, sort, and folder filtering.
     */
    public function list(Request $request)
    {
        return $this->browseContent($request);
    }

    /**
     * Show documents by category with file explorer interface
     */
    public function byCategory(Category $category, Request $request)
    {
        return $this->browseContent($request, $category);
    }

    /**
     * Unified content browser - works for both all documents and category view
     */
    private function browseContent(Request $request, Category $category = null)
    {
        $currentFolder = null;
        $breadcrumbs = [];

        // Get current folder from request
        if ($request->has('folder_id') && $request->folder_id != '') {
            $currentFolder = Folder::find($request->folder_id);
        }

        // Build breadcrumbs if we're in a folder
        if ($currentFolder) {
            $breadcrumbs = $this->buildBreadcrumbs($currentFolder, $category);
        } else {
            // Root level breadcrumbs
            $breadcrumbs[] = [
                'name' => $category ? $category->name : 'All Documents',
                'url' => $category ? route('documents.category', $category) : route('documents.list')
            ];
        }

        // Get content for current location
        $content = $this->getContent($request, $category, $currentFolder);

        $categories = Category::withCount('documents')->get();
        $folders = Folder::withCount('documents')->get();

        return view('documents.explorer', compact(
            'content',
            'categories',
            'folders',
            'category',
            'currentFolder',
            'breadcrumbs'
        ));
    }

    /**
     * Build breadcrumbs for navigation
     */
    private function buildBreadcrumbs($folder, $category = null)
    {
        $breadcrumbs = [];
        $current = $folder;

        // Add current folder and parents
        while ($current) {
            $breadcrumbs[] = [
                'name' => $current->name,
                'url' => $category
                    ? route('documents.category', ['category' => $category, 'folder_id' => $current->id])
                    : route('documents.list', ['folder_id' => $current->id])
            ];
            $current = $current->parent;
        }

        // Add category or root
        if ($category) {
            $breadcrumbs[] = [
                'name' => $category->name,
                'url' => route('documents.category', $category)
            ];
        }

        $breadcrumbs[] = [
            'name' => 'Root',
            'url' => $category ? route('documents.category', $category) : route('documents.list')
        ];

        return array_reverse($breadcrumbs);
    }

    /**
     * Get content (folders and documents) for current location
     */
    private function getContent(Request $request, $category = null, $currentFolder = null)
    {
        $content = [];

        // Get folders
        $folderQuery = Folder::query();

        if ($currentFolder) {
            // Subfolders of current folder
            $folderQuery->where('parent_id', $currentFolder->id);
        } else {
            // Top-level folders
            $folderQuery->whereNull('parent_id');
        }

        if ($category) {
            $folderQuery->where('category_id', $category->id);
        }

        $content['folders'] = $folderQuery->withCount(['documents', 'children'])
                                        ->orderBy('name')
                                        ->get();

        // Get documents
        $documentQuery = Document::with(['user', 'folder', 'documentCategory']);

        if ($currentFolder) {
            $documentQuery->where('folder_id', $currentFolder->id);
        } else {
            $documentQuery->whereNull('folder_id');
        }

        if ($category) {
            $documentQuery->where('category_id', $category->id);
        }

        // Apply search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $documentQuery->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply filters
        if ($request->has('type')) {
            switch ($request->type) {
                case 'pdf':
                    $documentQuery->where('file_path', 'like', '%.pdf');
                    break;
                case 'word':
                    $documentQuery->where(function($q) {
                        $q->where('file_path', 'like', '%.doc')
                          ->orWhere('file_path', 'like', '%.docx');
                    });
                    break;
                case 'excel':
                    $documentQuery->where(function($q) {
                        $q->where('file_path', 'like', '%.xlsx')
                          ->orWhere('file_path', 'like', '%.xls');
                    });
                    break;
            }
        }

        // Apply sorting
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'latest':
                $documentQuery->latest();
                break;
            case 'oldest':
                $documentQuery->oldest();
                break;
            case 'type':
                $documentQuery->orderBy('file_path');
                break;
            case 'size':
                // Assuming you have file_size column
                $documentQuery->orderBy('file_size', 'desc');
                break;
            default: // name
                $documentQuery->orderBy('title');
                break;
        }

        $content['documents'] = $documentQuery->get();

        return $content;
    }

    /**
     * Show HR documents (Staff Contracts, Sick Notes)
     */
    public function hrDocuments()
    {
        $hrCategoryIds = Category::whereIn('name', ['Staff Contracts', 'Sick Notes'])->pluck('id');

        $request = request();
        $request->merge(['category_id' => $hrCategoryIds->first()]);

        return $this->browseContent($request, Category::find($hrCategoryIds->first()));
    }

    /**
     * Show Finance documents (Donor Contracts, Budgets, Petty Cash)
     */
    public function financeDocuments()
    {
        $financeCategoryIds = Category::whereIn('name', ['Donor Contracts', 'Budgets', 'Petty Cash'])->pluck('id');

        $request = request();
        $request->merge(['category_id' => $financeCategoryIds->first()]);

        return $this->browseContent($request, Category::find($financeCategoryIds->first()));
    }

    /**
     * Store uploaded document with folder and category support.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,pptx|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'file_name' => $request->title,
            'created_by' => Auth::id(),
            'folder_id' => $request->folder_id,
            'category_id' => $request->category_id,
        ]);

        // Create audit trail
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'uploaded',
            'document_id' => $document->id,
            'description' => 'Uploaded document: ' . $document->title,
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show the document details (View)
     */
    public function show(Document $document)
    {
        // Create audit trail for viewing
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'viewed',
            'document_id' => $document->id,
            'description' => 'Viewed document: ' . $document->title,
        ]);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        $folders = Folder::all();
        $categories = Category::all();
        return view('documents.edit', compact('document', 'folders', 'categories'));
    }

    /**
     * Update document with folder and category support.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,pptx|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $oldTitle = $document->title;

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $path = $request->file('file')->store('documents', 'public');
            $document->file_path = $path;
            $document->file_name = $request->title;
        }

        $document->title = $request->title;
        $document->description = $request->description;
        $document->folder_id = $request->folder_id;
        $document->category_id = $request->category_id;
        $document->save();

        // Create audit trail for update
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'updated',
            'document_id' => $document->id,
            'description' => 'Updated document from "' . $oldTitle . '" to "' . $document->title . '"',
        ]);

        return redirect()->route('documents.list')->with('success', 'Document updated successfully.');
    }

    /**
     * Soft delete document (move to recycle bin)
     */
    public function destroy(Document $document)
    {
        // Create audit trail before deletion
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'document_id' => $document->id,
            'description' => 'Moved document to recycle bin: ' . $document->title,
        ]);

        // Soft delete (move to recycle bin)
        $document->update(['deleted_at' => now()]);

        return redirect()->route('documents.list')->with('success', 'Document moved to recycle bin successfully.');
    }

    /**
     * Show recycle bin (soft deleted documents)
     */
    public function recycleBin()
    {
        $deletedDocuments = Document::onlyTrashed()
            ->with(['user', 'folder', 'documentCategory'])
            ->latest()
            ->get();

        return view('documents.recycle-bin', compact('deletedDocuments'));
    }

    /**
     * Restore document from recycle bin
     */
    public function restore($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);
        $document->restore();

        // Create audit trail for restoration
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'restored',
            'document_id' => $document->id,
            'description' => 'Restored document from recycle bin: ' . $document->title,
        ]);

        return redirect()->route('documents.recycle-bin')->with('success', 'Document restored successfully.');
    }

    /**
     * Permanently delete document
     */
    public function forceDelete($id)
    {
        $document = Document::onlyTrashed()->findOrFail($id);

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);

        // Create audit trail before permanent deletion
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'permanently_deleted',
            'document_id' => $document->id,
            'description' => 'Permanently deleted document: ' . $document->title,
        ]);

        // Permanently delete
        $document->forceDelete();

        return redirect()->route('documents.recycle-bin')->with('success', 'Document permanently deleted.');
    }

    /**
     * Show audit trails
     */
    public function auditTrails()
    {
        $auditTrails = AuditTrail::with(['user', 'document'])
            ->latest()
            ->paginate(20);

        return view('documents.audit-trails', compact('auditTrails'));
    }

    /**
     * Download document
     */
    public function download(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Create audit trail for download
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'downloaded',
            'document_id' => $document->id,
            'description' => 'Downloaded document: ' . $document->title,
        ]);

        return response()->download($filePath, $document->title . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    // Keep your existing methods for backward compatibility
    public function donorContractsIndex()
    {
        return $this->financeDocuments();
    }

    public function budgetsIndex()
    {
        return $this->financeDocuments();
    }

    public function checkFile(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['exists' => false]);
        }

        return response()->json(['exists' => true]);
    }

    public function expiring()
    {
        $expiringDocs = Document::where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->with('user')
            ->latest()
            ->get();

        return view('documents.expiring', compact('expiringDocs'));
    }
}
