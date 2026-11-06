@extends('layouts.admin')

@section('title', 'Manajemen Admin')

@section('content')
<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Manajemen Admin</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Kelola akun administrator.</p>
    </div>
    <a href="{{ route('admin.admins.create') }}" class="btn-modern btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Admin</a>
</div>

<div class="card-modern">
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $a)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $a->name }}</td>
                    <td>{{ $a->email }}</td>
                    <td>{{ $a->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.admins.edit', $a) }}" class="btn-modern btn-outline btn-sm">Edit</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center; padding:48px; color:var(--text-muted);">Belum ada admin</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px;">{{ $admins->links() }}</div>
</div>
@endsection