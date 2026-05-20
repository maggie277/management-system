<?php

namespace App\Http\Controllers;

use App\Models\Consultant;
use Illuminate\Http\Request;

class ConsultantController extends Controller
{
    public function index()
    {
        $consultants = Consultant::latest()->paginate(10);
        $totalConsultants = Consultant::count();
        $consultantsBudget = Consultant::sum('budget');
        $activeConsultants = Consultant::where('status', 'active')->count();
        $inactiveConsultants = Consultant::where('status', 'inactive')->count();

        return view('consultants.index', compact(
            'consultants',
            'totalConsultants',
            'consultantsBudget',
            'activeConsultants',
            'inactiveConsultants'
        ));
    }

    public function create()
    {
        return view('consultants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email',
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        ]);

        Consultant::create($validated);

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant added successfully!');
    }

    public function show(Consultant $consultant)
    {
        return view('consultants.show', compact('consultant'));
    }

    public function edit(Consultant $consultant)
    {
        return view('consultants.edit', compact('consultant'));
    }

    public function update(Request $request, Consultant $consultant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:consultants,email,' . $consultant->id,
            'phone' => 'nullable|string|max:20',
            'specialization' => 'nullable|string|max:255',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
        ]);

        $consultant->update($validated);

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant updated successfully!');
    }

    public function destroy(Consultant $consultant)
    {
        $consultant->delete();

        return redirect()->route('consultants.index')
            ->with('success', 'Consultant deleted successfully!');
    }

    public function toggleStatus(Consultant $consultant)
    {
        $consultant->status = $consultant->status === 'active' ? 'inactive' : 'active';
        $consultant->save();

        return redirect()->back()
            ->with('success', 'Consultant status updated successfully!');
    }
}
