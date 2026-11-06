@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Detail Pengguna</h2>
</div>

<div class="card-modern" style="max-width: 600px;">
    <p><strong>Nama:</strong> {{ $user->name }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role }}</p>
    <p><strong>Tanggal Daftar:</strong> {{ $user->created_at->format('d M Y') }}</p>

    <form method="POST" action="{{ route('admin.users.role', $user) }}" style="margin-top: 16px;">
        @csrf
        @method('PATCH')
        <label>Ubah Role</label>
        <select name="role" class="form-control" style="margin: 8px 0;">
            <option value="user" @selected($user->role === 'user')>User</option>
            <option value="admin" @selected($user->role === 'admin')>Admin</option>
        </select>
        <button class="btn-modern btn-primary btn-sm" type="submit">Simpan</button>
    </form>

    <div style="margin-top: 16px;">
        <a href="{{ route('admin.users') }}" class="btn-modern btn-outline btn-sm">Kembali</a>
    </div>
</div>
@endsection