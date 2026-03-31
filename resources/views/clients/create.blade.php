@extends('layouts.app')
@section('title','Add Client')
@section('content')
<div class="page-header">
    <div class="page-title">Add New Client</div>
    <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back
    </a>
</div>
<div class="form-card" style="max-width:700px">
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Company / Business</label>
                <input type="text" name="company" class="form-control" value="{{ old('company') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">VAT / Tax Number</label>
                <input type="text" name="vat_number" class="form-control" value="{{ old('vat_number') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Client Type *</label>
                <select name="type" class="form-select" required>
                    <option value="retail" {{ old('type')=='retail'?'selected':'' }}>Retail</option>
                    <option value="wholesale" {{ old('type')=='wholesale'?'selected':'' }}>Wholesale</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Save Client
                </button>
                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection