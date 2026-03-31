@extends('layouts.app')
@section('title','Units')
@section('content')
<div class="page-header">
    <div class="page-title">Units of Measurement</div>
</div>
<div class="row g-4">
    <div class="col-md-4">
        <div class="form-card">
            <h6 class="fw-700 mb-3">Add New Unit</h6>
            <form method="POST" action="{{ route('admin.units.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Unit Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Kilogram" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Short Name</label>
                    <input type="text" name="short_name" class="form-control" placeholder="e.g. KG" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Add Unit</button>
            </form>
        </div>
    </div>
    <div class="col-md-8">
        <div class="table-card">
            <div class="table-card-header">
                <span class="table-card-title">All Units</span>
            </div>
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Short</th>
                        <th>Products</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($units as $u)
                    <tr>
                        <td>{{ $u->id }}</td>
                        <td>{{ $u->name }}</td>
                        <td><span class="badge bg-light text-dark">{{ $u->short_name }}</span></td>
                        <td>{{ $u->products_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.units.destroy',$u) }}"
                                onsubmit="return confirm('Delete unit?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection