@extends('layouts.app')
@section('title','Backup Database')
@section('content')
<div class="page-header">
    <div class="page-title">Backup Database</div>
    <form method="POST" action="{{ route('admin.backup.create') }}">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-database-down me-1"></i> Create Backup Now
        </button>
    </form>
</div>

<div class="table-card">
    <div class="table-card-header">
        <span class="table-card-title">Backup Files</span>
        <span class="text-muted" style="font-size:12px">Stored in storage/app/backups/</span>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Filename</th>
                    <th>Size</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                <tr>
                    <td><i class="bi bi-file-zip me-2 text-warning"></i>{{ $file['name'] }}</td>
                    <td>{{ round($file['size']/1024,1) }} KB</td>
                    <td>{{ date('d M Y H:i', $file['date']) }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.backup.download', $file['name']) }}"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                            <form method="POST" action="{{ route('admin.backup.destroy', $file['name']) }}"
                                onsubmit="return confirm('Delete this backup?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-database-x fs-2 d-block mb-2"></i>
                        No backups yet. Click "Create Backup Now" to start.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection