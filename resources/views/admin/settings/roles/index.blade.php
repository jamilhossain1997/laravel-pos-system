@extends('layouts.app')
@section('title','Roles & Permissions')
@section('content')
<div class="page-header">
    <div class="page-title">Roles & Permissions</div>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Role
    </a>
</div>
<div class="row g-3">
    @foreach($roles as $role)
    <div class="col-md-4">
        <div class="form-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-700 fs-6">{{ $role->name }}</div>
                    <div class="text-muted" style="font-size:11px">{{ $role->users_count }} users</div>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                    {{ $role->slug }}
                </span>
            </div>
            <div class="mb-3">
                @if(in_array('*', $role->permissions ?? []))
                <span class="badge bg-success">All Permissions</span>
                @else
                @foreach($role->permissions ?? [] as $perm)
                <span class="badge bg-light text-dark me-1 mb-1">{{ $perm }}</span>
                @endforeach
                @endif
            </div>
            @if($role->slug !== 'super-admin')
            <form method="POST" action="{{ route('admin.roles.destroy',$role) }}"
                onsubmit="return confirm('Delete this role?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection