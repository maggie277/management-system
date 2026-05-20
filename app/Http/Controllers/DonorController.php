<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\Document;
use App\Models\Category;
use App\Models\Consultant; // Fixed: Changed from Illuminate\Model\Consultant to App\Models\Consultant
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonorController extends Controller
{
    public function index(Request $request)
    {
        // Get all donors for statistics (no pagination needed for stats)
        $donors = Donor::query();

        // Apply search filter if needed for stats
        if (!empty($request->search)) {
            $donors->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_number', 'like', '%' . $request->search . '%')
                  ->orWhere('responsible_person', 'like', '%' . $request->search . '%');
            });
        }

        $allDonors = $donors->get();

        // Calculate statistics
        $stats = [
            'totalDonors' => $allDonors->count(),
            'activeDonors' => $allDonors->where('is_active', true)->count(),
            'totalBudget' => $allDonors->sum('total_budget'),
            'activeContracts' => $allDonors->filter(function($donor) {
                return $donor->contract_end_date >= now();
            })->count(),
        ];

        // Consultants data
        $totalConsultants = Consultant::count();
        $consultantsBudget = Consultant::sum('budget');
        $activeConsultants = Consultant::where('status', 'active')->count();

        // Merge stats with consultants data
        $data = array_merge($stats, [
            'totalConsultants' => $totalConsultants,
            'consultantsBudget' => $consultantsBudget,
            'activeConsultants' => $activeConsultants,
        ]);

        return view('donors.index', $data);
    }

    // Add this method to your DonorController
    public function list(Request $request)
    {
        // Get filter parameters
        $search = $request->get('search', '');

        // Start query - soft deleted donors are automatically excluded
        $query = Donor::query();

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('responsible_person', 'like', '%' . $search . '%');
            });
        }

        // Get paginated results
        $donors = $query->orderBy('name')->paginate(12);

        return view('donors.list', [
            'donors' => $donors,
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('donors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'contract_type' => 'required|in:monthly,quarterly,yearly',
            'contract_start_date' => 'required|date',
            'donation_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'responsible_person' => 'required|string|max:255',
        ]);

        // DEBUG: Log that we're starting donor creation
        Log::info('=== CREATING DONOR ===');
        Log::info('Donor Name: ' . $validated['name']);
        Log::info('Has Document: ' . ($request->hasFile('document') ? 'YES' : 'NO'));

        // Calculate contract end date based on type
        $contractEndDate = $this->calculateContractEndDate(
            $validated['contract_start_date'],
            $validated['contract_type']
        );

        $documentPath = null;
        if ($request->hasFile('document')) {
            Log::info('=== PROCESSING DOCUMENT UPLOAD ===');
            $documentPath = $request->file('document')->store('donor-documents', 'public');
            Log::info('Document stored at: ' . $documentPath);

            // Automatically create document record in Donor Contracts category
            $this->createDonorContractDocument($request, $documentPath, $validated);
        } else {
            Log::info('=== NO DOCUMENT UPLOADED ===');
        }

        $donor = Donor::create([
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'contract_type' => $validated['contract_type'],
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $contractEndDate,
            'total_budget' => $validated['donation_amount'],
            'remaining_budget' => $validated['donation_amount'],
            'budget_threshold' => 0,
            'notes' => $validated['notes'],
            'document_path' => $documentPath,
            'responsible_person' => $validated['responsible_person'],
            'is_active' => true,
        ]);

        Log::info('=== DONOR CREATED SUCCESSFULLY ===');
        Log::info('Donor ID: ' . $donor->id);

        return redirect()->route('donors.index')->with('success', 'Donor created successfully.');
    }

    public function show(Donor $donor)
    {
        return view('donors.show', compact('donor'));
    }

    public function edit(Donor $donor)
    {
        return view('donors.edit', compact('donor'));
    }

    public function update(Request $request, Donor $donor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'contract_type' => 'required|in:monthly,quarterly,yearly',
            'contract_start_date' => 'required|date',
            'donation_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'responsible_person' => 'required|string|max:255',
        ]);

        // Calculate contract end date based on type
        $contractEndDate = $this->calculateContractEndDate(
            $validated['contract_start_date'],
            $validated['contract_type']
        );

        $updateData = [
            'name' => $validated['name'],
            'contact_number' => $validated['contact_number'],
            'email' => $validated['email'],
            'contract_type' => $validated['contract_type'],
            'contract_start_date' => $validated['contract_start_date'],
            'contract_end_date' => $contractEndDate,
            'total_budget' => $validated['donation_amount'],
            'remaining_budget' => $validated['donation_amount'],
            'notes' => $validated['notes'],
            'responsible_person' => $validated['responsible_person'],
        ];

        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($donor->document_path) {
                Storage::disk('public')->delete($donor->document_path);
            }

            $documentPath = $request->file('document')->store('donor-documents', 'public');
            $updateData['document_path'] = $documentPath;

            // Automatically create/update document record in Donor Contracts category
            $this->createDonorContractDocument($request, $documentPath, $validated, $donor);
        }

        $donor->update($updateData);

        return redirect()->route('donors.index')->with('success', 'Donor updated successfully.');
    }

    public function destroy(Donor $donor)
    {
        // Soft delete the donor
        $donor->delete();

        return redirect()->route('donors.index')->with('success', 'Donor deleted successfully.');
    }

    public function downloadDocument(Donor $donor)
    {
        if (!$donor->document_path) {
            return back()->with('error', 'No document found.');
        }

        if (!Storage::disk('public')->exists($donor->document_path)) {
            return back()->with('error', 'Document file not found.');
        }

        return Storage::disk('public')->download($donor->document_path);
    }

    // Trashed donors management methods
    public function trashed(Request $request)
    {
        // Get only soft deleted donors
        $query = Donor::onlyTrashed();

        $search = $request->get('search', '');
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('contact_number', 'like', '%' . $search . '%')
                  ->orWhere('responsible_person', 'like', '%' . $search . '%');
            });
        }

        $donors = $query->orderBy('deleted_at', 'desc')->paginate(10);

        return view('donors.trashed', compact('donors', 'search'));
    }

    public function restore($id)
    {
        $donor = Donor::onlyTrashed()->findOrFail($id);
        $donor->restore();

        return redirect()->route('donors.trashed')->with('success', 'Donor restored successfully.');
    }

    public function forceDelete($id)
    {
        $donor = Donor::onlyTrashed()->findOrFail($id);

        // Delete document if exists
        if ($donor->document_path) {
            Storage::disk('public')->delete($donor->document_path);
        }

        $donor->forceDelete();

        return redirect()->route('donors.trashed')->with('success', 'Donor permanently deleted.');
    }

    public function emptyTrash()
    {
        $trashedDonors = Donor::onlyTrashed()->get();

        foreach ($trashedDonors as $donor) {
            // Delete document if exists
            if ($donor->document_path) {
                Storage::disk('public')->delete($donor->document_path);
            }
            $donor->forceDelete();
        }

        return redirect()->route('donors.trashed')->with('success', 'All donors permanently deleted.');
    }

    /**
     * Create a document record in the Donor Contracts category
     */
    private function createDonorContractDocument(Request $request, $documentPath, $validated, $donor = null)
    {
        try {
            Log::info('=== STARTING DONOR CONTRACT DOCUMENT CREATION ===');
            Log::info('Donor Name: ' . $validated['name']);
            Log::info('Document Path: ' . $documentPath);

            // Find Donor Contracts category
            $donorCategory = Category::where('name', 'Donor Contracts')->first();

            if (!$donorCategory) {
                Log::error('Donor Contracts category not found!');
                return null;
            }

            Log::info('Using Donor Contracts category - ID: ' . $donorCategory->id);

            // Prepare document data - NO FOLDER, just category
            $documentData = [
                'title' => 'Donor Contract - ' . $validated['name'],
                'description' => "Donor contract for {$validated['name']}. " .
                               "Contract Type: {$validated['contract_type']}, " .
                               "Start Date: {$validated['contract_start_date']}, " .
                               "Amount: K" . number_format($validated['donation_amount'], 2) . ", " .
                               "Responsible Person: {$validated['responsible_person']}" .
                               ($validated['notes'] ? "\nNotes: {$validated['notes']}" : ''),
                'file_path' => $documentPath,
                'file_name' => $request->file('document')->getClientOriginalName(),
                'created_by' => Auth::id(),
                'folder_id' => null, // No folder
                'category_id' => $donorCategory->id, // Directly to Donor Contracts category
                'document_type' => 'donor_contract',
                'donor_name' => $validated['name'],
            ];

            Log::info('Document data prepared:', $documentData);

            // If updating an existing donor, update the existing document
            if ($donor && $donor->document_path) {
                $existingDocument = Document::where('file_path', $donor->document_path)->first();
                if ($existingDocument) {
                    Log::info('Updating existing document', ['document_id' => $existingDocument->id]);
                    $existingDocument->update($documentData);
                    return $existingDocument;
                }
            }

            // Create new document
            Log::info('Creating new document');
            $document = Document::create($documentData);
            Log::info('=== DOCUMENT CREATED SUCCESSFULLY ===');
            Log::info('Document ID: ' . $document->id);
            Log::info('Document Title: ' . $document->title);
            Log::info('Category ID: ' . $document->category_id);

            return $document;

        } catch (\Exception $e) {
            Log::error('=== FAILED TO CREATE DONOR CONTRACT DOCUMENT ===');
            Log::error('Error: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    private function calculateContractEndDate($startDate, $contractType)
    {
        $start = \Carbon\Carbon::parse($startDate);

        switch ($contractType) {
            case 'monthly':
                return $start->addMonth();
            case 'quarterly':
                return $start->addMonths(3);
            case 'yearly':
                return $start->addYear();
            default:
                return $start->addYear();
        }
    }
}
