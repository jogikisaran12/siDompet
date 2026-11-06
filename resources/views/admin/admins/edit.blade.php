@extends('layouts.admin')

@section('title', 'Edit Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Edit Admin</h2>
</div>

<div class="card-modern" style="max-width: 600px;">
    <form method="POST" action="{{ route('admin.admins.update', $user) }}">
        @csrf
        @method('PATCH')
        <div style="margin-bottom: 12px;">
            <label>Nama</label>
            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required>
        </div>
        <div style="margin-bottom: 12px;">
            <label>Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required>
        </div>
        <button class="btn-modern btn-primary btn-sm" type="submit">Simpan</button>
    </form>

    <hr style="margin: 20px 0;">

    <form method="POST" action="{{ route('admin.admins.password', $user) }}">
        @csrf
        @method('PATCH')
        <div style="margin-bottom: 12px;">
            <label>Password Baru</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div style="margin-bottom: 12px;">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button class="btn-modern btn-primary btn-sm" type="submit">Ubah Password</button>
    </form>

    <div style="margin-top: 16px;">
        <a href="{{ route('admin.admins') }}" class="btn-modern btn-outline btn-sm">Kembali</a>
    </div>
</div>
@endsection