@extends('layouts.app')
@section('title','Settings')
@section('content')
<div class="page-header">
    <div class="page-title">System Settings</div>
</div>
<div class="form-card" style="max-width:720px">
    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        <h6 class="text-muted text-uppercase fw-600 mb-3" style="font-size:11px;letter-spacing:.07em">
            Company Information
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control"
                    value="{{ $settings->get('general')?->where('key','company_name')->first()?->value }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="company_phone" class="form-control"
                    value="{{ $settings->get('general')?->where('key','company_phone')->first()?->value }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="company_email" class="form-control"
                    value="{{ $settings->get('general')?->where('key','company_email')->first()?->value }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">VAT Number (ZATCA)</label>
                <input type="text" name="company_vat" class="form-control"
                    value="{{ $settings->get('general')?->where('key','company_vat')->first()?->value }}">
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea name="company_address" class="form-control" rows="2">{{ $settings->get('general')?->where('key','company_address')->first()?->value }}</textarea>
            </div>
        </div>

        <hr class="my-4">
        <h6 class="text-muted text-uppercase fw-600 mb-3" style="font-size:11px;letter-spacing:.07em">
            Invoice & Tax
        </h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Default Tax Rate (%)</label>
                <input type="number" name="tax_rate" class="form-control" step="0.01"
                    value="{{ $settings->get('invoice')?->where('key','tax_rate')->first()?->value ?? 15 }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Invoice Prefix</label>
                <input type="text" name="invoice_prefix" class="form-control"
                    value="{{ $settings->get('invoice')?->where('key','invoice_prefix')->first()?->value ?? 'INV-' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Quotation Prefix</label>
                <input type="text" name="quotation_prefix" class="form-control"
                    value="{{ $settings->get('invoice')?->where('key','quotation_prefix')->first()?->value ?? 'QT-' }}">
            </div>
            <div class="col-12">
                <label class="form-label">Default Invoice Note</label>
                <input type="text" name="invoice_note" class="form-control"
                    value="{{ $settings->get('invoice')?->where('key','invoice_note')->first()?->value }}">
            </div>
        </div>

        <hr class="my-4">
        <h6 class="text-muted text-uppercase fw-600 mb-3" style="font-size:11px;letter-spacing:.07em">
            Stock Management
        </h6>
        <div class="col-md-4">
            <label class="form-label">Default Low Stock Alert Qty</label>
            <input type="number" name="low_stock_alert" class="form-control"
                value="{{ $settings->get('stock')?->where('key','low_stock_alert')->first()?->value ?? 5 }}">
        </div>

        <hr class="my-4">
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-save me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection