@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Dashboard Admin</h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Pengguna</h6>
                    <h3>{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Pengguna Terbaru</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentUsers as $u)
                    <tr>
                        <td>{{ $u->name }}</td>
                        <td>{{ $u->email }}</td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.users') }}" class="btn btn-primary">Lihat Semua Pengguna</a>
    </div>
</div>
@endsection