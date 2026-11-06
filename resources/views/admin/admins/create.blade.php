@extends('layouts.admin')

@section('title', 'Tambah Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Tambah Admin</h2>
</div>

<div class="card-modern" style="max-width: 600px;">
    <form method="POST" action="{{ route('admin.admins.store') }}">
        @csrf
        <div style="margin-bottom: 12px;">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div style="margin-bottom: 12px;">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div style="margin-bottom: 12px;">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div style="margin-bottom: 12px;">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button class="btn-modern btn-primary btn-sm" type="submit">Simpan</button>
        <a href="{{ route('admin.admins') }}" class="btn-modern btn-outline btn-sm">Batal</a>
    </form>
</div>
@endsection