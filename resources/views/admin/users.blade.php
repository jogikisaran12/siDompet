@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Manajemen Pengguna</h2>
    <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Cari dan filter akun pengguna.</p>
</div>

<div class="card-modern" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.users') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="form-control" style="flex: 1; min-width: 200px;">
        <select name="role" class="form-control" style="min-width: 120px;">
            <option value="">Semua Role</option>
            <option value="user" @selected(request('role') === 'user')>User</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
        </select>
        <button class="btn-modern btn-primary btn-sm" type="submit">Filter</button>
    </form>
</div>

<div class="card-modern">
    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tanggal Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                <tr>
                    <td style="font-weight: 600; color: var(--text-primary);">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        @if($u->role === 'admin')
                            <span class="badge-modern" style="background: #EDE9FE; color: #6D28D9;">Admin</span>
                        @else
                            <span class="badge-modern badge-income">User</span>
                        @endif
                    </td>
                    <td>{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $u) }}" class="btn-modern btn-outline btn-sm">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 48px; color: var(--text-muted);">Belum ada pengguna</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px;">
        {{ $users->links() }}
    </div>
</div>
@endsection