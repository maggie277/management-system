<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        return view('documents.institutional.index', compact('documents'));
    }

    public function create()
    {
        if (!auth()->user()->isManagement()) {
            abort(403, 'Unauthorized action.');
        }
        return view('documents.institutional.create');
    }

    public function store(Request $request)
    {
        // DEBUG: Log all request data
        Log::info('=== UPLOAD DEBUG START ===');
        Log::info('User ID: ' . Auth::id());
        Log::info('All request data:', $request->all());
        Log::info('Has file? ' . ($request->hasFile('document') ? 'YES' : 'NO'));

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            Log::info('File name: ' . $file->getClientOriginalName());
            Log::info('File size: ' . $file->getSize());
            Log::info('File mime: ' . $file->getMimeType());
            Log::info('File valid? ' . ($file->isValid() ? 'YES' : 'NO'));
            if (!$file->isValid()) {
                Log::error('File error: ' . $file->getError());
            }
        }

        // Check authorization
        if (!auth()->user()->isManagement()) {
            Log::error('Unauthorized user: ' . Auth::id());
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Check if file exists
        if (!$request->hasFile('document')) {
            Log::error('No file in request');
            return redirect()->back()->with('error', 'No file was selected. Please choose a file to upload.');
        }

        $file = $request->file('document');

        if (!$file->isValid()) {
            Log::error('Invalid file. Error code: ' . $file->getError());
            $errorMessages = [
                1 => 'File exceeds upload_max_filesize directive in php.ini',
                2 => 'File exceeds MAX_FILE_SIZE directive in HTML form',
                3 => 'File was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload',
            ];
            $errorMsg = $errorMessages[$file->getError()] ?? 'Unknown upload error';
            return redirect()->back()->with('error', 'File upload failed: ' . $errorMsg);
        }

        // Validate request
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'document' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            ]);
            Log::info('Validation passed');
        } catch (\Exception $e) {
            Log::error('Validation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Validation failed: ' . $e->getMessage())
                ->withInput();
        }

        try {
            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName) . '.' . $extension;

            Log::info('Generated filename: ' . $safeName);

            // Store the file
            $filePath = $file->storeAs('institutional-documents', $safeName, 'public');

            if (!$filePath) {
                throw new \Exception('Failed to store file - storeAs returned false');
            }

            Log::info('File stored at: ' . $filePath);
            Log::info('Full path: ' . storage_path('app/public/' . $filePath));

            // Create database record
            $document = InstitutionalDocument::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'file_path' => $filePath,
                'file_extension' => $extension,
                'file_size' => $file->getSize(),
                'uploaded_by' => Auth::id(),
                'is_public' => true,
                'download_count' => 0,
            ]);

            Log::info('Database record created with ID: ' . $document->id);
            Log::info('=== UPLOAD DEBUG END ===');

            return redirect()->route('documents.institutional.index')
                ->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::info('=== UPLOAD DEBUG END ===');

            return redirect()->back()
                ->with('error', 'Failed to upload document: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $document = InstitutionalDocument::findOrFail($id);
        $document->increment('download_count');

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        $filename = $document->name . '.' . $document->file_extension;

        return response()->download($filePath, $filename);
    }

    public function destroy($id)
    {
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
