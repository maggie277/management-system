<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Documents dashboard.
     */
    public function index()
    {
        return view('documents.index'); // Documents dashboard with buttons
    }

    /**
     * Show the upload form.
     */
    public function create()
    {
        return view('documents.create'); // Upload form
    }

    /**
     * Store uploaded document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xlsx,pptx|max:10240',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }

    /**
     * List all uploaded documents.
     */
    public function list()
    {
        $documents = Document::latest()->get();
        return view('documents.list', compact('documents')); // Documents list
    }

    /**
     * Show edit form.
     */
    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    /**
     * Update document details.
     */
    public function update(Request $request, Document $document)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $document->update($request->only('title', 'description'));

        return redirect()->route('documents.list')->with('success', 'Document updated successfully.');
    }

    /**
     * Delete a document.
     */
    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('documents.list')->with('success', 'Document deleted successfully.');
    }
}
