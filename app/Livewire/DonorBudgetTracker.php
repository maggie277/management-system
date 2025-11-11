<?php
namespace App\Http\Livewire;

use App\Models\Donor;
use App\Models\DonorBudgetTransaction;
use Livewire\Component;

class DonorBudgetTracker extends Component
{
    public $donor;
    public $transactions = [];
    public $showTransactionModal = false;

    public $form = [
        'description' => '',
        'amount' => '',
        'type' => 'expense',
        'transaction_date' => '',
        'notes' => ''
    ];

    protected $rules = [
        'form.description' => 'required|string|max:255',
        'form.amount' => 'required|numeric|min:0.01',
        'form.type' => 'required|in:income,expense',
        'form.transaction_date' => 'required|date',
        'form.notes' => 'nullable|string'
    ];

    public function mount(Donor $donor)
    {
        $this->donor = $donor;
        $this->loadTransactions();
        $this->form['transaction_date'] = now()->format('Y-m-d');
    }

    public function loadTransactions()
    {
        $this->transactions = $this->donor->budgetTransactions()
            ->with('creator')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function addTransaction()
    {
        $this->validate();

        $amount = $this->form['amount'];

        if ($this->form['type'] === 'expense') {
            $this->donor->remaining_budget -= $amount;
        } else {
            $this->donor->remaining_budget += $amount;
        }

        $this->donor->save();

        DonorBudgetTransaction::create([
            'donor_id' => $this->donor->id,
            'description' => $this->form['description'],
            'amount' => $amount,
            'type' => $this->form['type'],
            'transaction_date' => $this->form['transaction_date'],
            'created_by' => auth()->id(),
            'notes' => $this->form['notes']
        ]);

        $this->donor->checkBudgetThreshold();
        $this->resetForm();
        $this->showTransactionModal = false;
        $this->loadTransactions();

        session()->flash('message', 'Transaction added successfully.');
    }

    public function deleteTransaction($transactionId)
    {
        $transaction = DonorBudgetTransaction::findOrFail($transactionId);

        if ($transaction->type === 'expense') {
            $this->donor->remaining_budget += $transaction->amount;
        } else {
            $this->donor->remaining_budget -= $transaction->amount;
        }

        $this->donor->save();
        $transaction->delete();
        $this->donor->checkBudgetThreshold();
        $this->loadTransactions();

        session()->flash('message', 'Transaction deleted successfully.');
    }

    private function resetForm()
    {
        $this->form = [
            'description' => '',
            'amount' => '',
            'type' => 'expense',
            'transaction_date' => now()->format('Y-m-d'),
            'notes' => ''
        ];
    }

    public function render()
    {
        return view('livewire.donor-budget-tracker');
    }
}
