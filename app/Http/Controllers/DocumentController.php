<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DocumentController extends Controller
{
    /**
     * Documents dashboard.
     */
    public function index()
    {
        return view('documents.index'); // Dashboard with buttons
    }

    /**
     * Show upload form.
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
        $documents = Document::with('uploadedBy')->latest()->get();
        return view('documents.list', compact('documents'));
    }

    /**
     * Show edit form for metadata or in-browser file editing.
     */
    public function edit(Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        $content = null;

        // Only for editable Word/Excel files
        if ($ext === 'docx' && file_exists($filePath)) {
            $phpWord = WordIOFactory::load($filePath);
            $content = '';
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $content .= $element->getText() . "\n";
                    }
                }
            }
        } elseif ($ext === 'xlsx' && file_exists($filePath)) {
            $spreadsheet = SpreadsheetIOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $content = $sheet->toArray(); // 2D array
        }

        return view('documents.edit', compact('document', 'content', 'ext'));
    }

    /**
     * Update document metadata or in-browser edited file.
     */
    public function update(Request $request, Document $document)
    {
        $filePath = storage_path('app/public/' . $document->file_path);
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);

        if ($request->has('content') && ($ext === 'docx' || $ext === 'xlsx')) {
            // Update in-browser editable file
            if ($ext === 'docx') {
                $phpWord = new \PhpOffice\PhpWord\PhpWord();
                $section = $phpWord->addSection();
                $lines = explode("\n", $request->content);
                foreach ($lines as $line) {
                    $section->addText($line);
                }
                $writer = WordIOFactory::createWriter($phpWord, 'Word2007');
                $writer->save($filePath);

            } elseif ($ext === 'xlsx') {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $rows = json_decode($request->content, true);

                foreach ($rows as $r => $row) {
                    foreach ($row as $c => $cell) {
                        $sheet->setCellValueByColumnAndRow($c + 1, $r + 1, $cell);
                    }
                }

                $writer = SpreadsheetIOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($filePath);
            }

        } else {
            // Update metadata or replace file
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'file' => 'nullable|file|mimes:pdf,doc,docx,xlsx,pptx|max:10240',
            ]);

            if ($request->hasFile('file')) {
                Storage::disk('public')->delete($document->file_path);
                $path = $request->file('file')->store('documents', 'public');
                $document->file_path = $path;
            }

            $document->title = $request->title;
            $document->description = $request->description;
            $document->save();
        }

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

    /**
     * Download / open document.
     */
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, "File not found.");
        }

        $ext = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $fileName = $document->title . '.' . $ext;

        return Storage::disk('public')->download($document->file_path, $fileName);
    }
}
