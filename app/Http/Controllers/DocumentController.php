<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Folder;
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
        $latestDocs = Document::with('uploadedBy')->latest()->take(5)->get();

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
        // Show all folders (since folders are now only for documents)
        $folders = Folder::all();
        return view('documents.create', compact('folders'));
    }

    /**
     * List all uploaded documents with search, sort, and folder filtering.
     */
    public function list(Request $request)
    {
        $folderId = $request->get('folder_id');
        $currentFolder = null;
        $subfolders = collect();

        // Get documents query
        $query = Document::with(['uploadedBy', 'folder']);

        // Handle folder filtering and get subfolders
        if ($request->has('folder_id') && $request->folder_id != '') {
            if ($request->folder_id == 'unassigned') {
                $query->whereNull('folder_id');
                // For unassigned, show all root folders
                $subfolders = Folder::whereNull('parent_id')
                    ->withCount('documents')
                    ->get();
            } else {
                $query->where('folder_id', $request->folder_id);
                $currentFolder = Folder::find($request->folder_id);
                // Get subfolders of the current folder
                $subfolders = Folder::where('parent_id', $request->folder_id)
                    ->withCount('documents')
                    ->get();
            }
        } else {
            // Root level - show only root folders
            $subfolders = Folder::whereNull('parent_id')
                ->withCount('documents')
                ->get();
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('uploadedBy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'type':
                $query->orderBy('file_path', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        // Type filtering
        if ($request->has('type')) {
            switch ($request->type) {
                case 'pdf':
                    $query->where('file_path', 'like', '%.pdf');
                    break;
                case 'word':
                    $query->where(function($q) {
                        $q->where('file_path', 'like', '%.doc')
                          ->orWhere('file_path', 'like', '%.docx');
                    });
                    break;
                case 'excel':
                    $query->where(function($q) {
                        $q->where('file_path', 'like', '%.xlsx')
                          ->orWhere('file_path', 'like', '%.xls');
                    });
                    break;
            }
        }

        $documents = $query->get();
        $filterType = $request->type;

        return view('documents.list', compact(
            'documents',
            'subfolders',
            'filterType',
            'currentFolder'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,pptx|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
            'folder_id' => $request->folder_id,
        ]);

        return redirect()->route('documents.list')->with('success', 'Document uploaded successfully.');
    }

    /**
     * Show the form for editing the specified document.
     */
    public function edit(Document $document)
    {
        // Show all folders
        $folders = Folder::all();
        return view('documents.edit', compact('document', 'folders'));
    }

    /**
     * Update document with folder support.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,xls,pptx|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $path = $request->file('file')->store('documents', 'public');
            $document->file_path = $path;
        }

        $document->title = $request->title;
        $document->description = $request->description;
        $document->folder_id = $request->folder_id;
        $document->save();

        return redirect()->route('documents.list')->with('success', 'Document updated successfully.');
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

        return response()->download($filePath, $document->title . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
    }

    /**
     * Folder management methods
     */
    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        Folder::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Folder created successfully.');
    }

    public function deleteFolder(Folder $folder)
    {
        Document::where('folder_id', $folder->id)->update(['folder_id' => null]);
        $folder->delete();

        return redirect()->back()->with('success', 'Folder deleted successfully.');
    }
}
