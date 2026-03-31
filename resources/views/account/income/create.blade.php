@extends('layouts.app')
@section('title','Add Income')
@section('content')
<div class="page-header">
    <div class="page-title">Add Income</div>
    <a href="{{ route('account.income.index') }}" class="btn btn-outline-secondary">← Back</a>
</div>
<div class="form-card" style="max-width:600px">
    <form method="POST" action="{{ route('account.income.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Category *</label>
                <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                    list="income-cats" placeholder="e.g. Sales, Rental, Other" required>
                <datalist id="income-cats">
                    <option>Sales</option>
                    <option>Service</option>
                    <option>Rental</option>
                    <option>Investment</option>
                    <option>Other</option>
                </datalist>
            </div>
            <div class="col-md-6">
                <label class="form-label">Amount (SAR) *</label>
                <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                    value="{{ old('amount') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Date *</label>
                <input type="date" name="income_date" class="form-control"
                    value="{{ old('income_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Reference No.</label>
                <input type="text" name="reference" class="form-control" value="{{ old('reference') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Note</label>
                <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success px-5">
                    <i class="bi bi-plus-circle me-1"></i> Save Income
                </button>
            </div>
        </div>
    </form>
</div>
@endsection