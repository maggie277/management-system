<?php
// app/Http/Livewire/DonorList.php

namespace App\Http\Livewire;

use App\Models\Donor;
use Livewire\Component;
use Livewire\WithPagination;

class DonorList extends Component
{
    use WithPagination;

    public $search = '';
    public $contractType = '';
    public $status = '';

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingContractType()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Donor::with('responsiblePersons');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        if ($this->contractType) {
            $query->where('contract_type', $this->contractType);
        }

        if ($this->status === 'active') {
            $query->where('is_active', true)
                  ->where('contract_end_date', '>=', now());
        } elseif ($this->status === 'inactive') {
            $query->where(function($q) {
                $q->where('is_active', false)
                  ->orWhere('contract_end_date', '<', now());
            });
        }

        if ($this->status === 'low_budget') {
            $query->where('is_budget_low', true);
        }

        $donors = $query->orderBy('name')->paginate(10);

        return view('livewire.donor-list', compact('donors'));
    }

    public function toggleStatus($donorId)
    {
        $donor = Donor::findOrFail($donorId);
        $donor->update(['is_active' => !$donor->is_active]);

        $this->dispatch('show-message', [
            'type' => 'success',
            'message' => 'Donor status updated successfully.'
        ]);
    }

    public function deleteDonor($donorId)
    {
        $donor = Donor::findOrFail($donorId);

        // Check if donor has transactions
        if ($donor->budgetTransactions()->exists()) {
            $this->dispatch('show-message', [
                'type' => 'error',
                'message' => 'Cannot delete donor with existing budget transactions.'
            ]);
            return;
        }

        // Delete document if exists
        if ($donor->document_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($donor->document_path);
        }

        $donor->delete();

        $this->dispatch('show-message', [
            'type' => 'success',
            'message' => 'Donor deleted successfully.'
        ]);
    }
}
