@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Expense</h1>

    <form action="{{ route('expenses.update', $expense) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="budget_id" class="form-label">Budget</label>
            <select name="budget_id" class="form-control" required>
                @foreach($budgets as $budget)
                    <option value="{{ $budget->id }}" {{ $expense->budget_id == $budget->id ? 'selected' : '' }}>
                        {{ $budget->description }} ({{ $budget->donor->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" value="{{ $expense->title }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="expense_date" class="form-label">Expense Date</label>
            <input type="date" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="notes" class="form-label">Notes (optional)</label>
            <textarea name="notes" class="form-control">{{ $expense->notes }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Expense</button>
        <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
