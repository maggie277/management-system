<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\InstitutionalDocument;

class InstitutionalDocumentController extends Controller
{
    public function index()
    {
        $documents = InstitutionalDocument::where('is_public', true)
            ->with('uploader')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        $categories = InstitutionalDocument::where('is_public', true)
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('documents.institutional.index', compact('documents', 'categories'));
    }

    public function create()
    {
        // Only allow management to upload documents
        if (!auth()->user()->isManagement()) {
            abort(403, 'Unauthorized action.');
        }

        return view('documents.institutional.create');
    }

    public function store(Request $request)
    {
        // Only allow management to upload documents
        if (!auth()->user()->isManagement()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'document' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('document');
            $extension = $file->getClientOriginalExtension();
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filePath = $file->store('institutional-documents', 'public');

            InstitutionalDocument::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'file_path' => $filePath,
                'file_extension' => $extension,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
                'is_public' => true,
            ]);

            return redirect()->route('documents.institutional.index')
                ->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error uploading document: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $document = InstitutionalDocument::where('is_public', true)->findOrFail($id);

        // Increment download count
        $document->increment('download_count');

        $filename = $document->name . '.' . $document->file_extension;

        return Storage::download($document->file_path, $filename);
    }

    public function destroy($id)
    {
        // Only allow management to delete documents
        if (!auth()->user()->isManagement()) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $document = InstitutionalDocument::findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return redirect()->route('documents.institutional.index')
                ->with('success', 'Document deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('documents.institutional.index')
                ->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }
}
