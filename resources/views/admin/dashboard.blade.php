@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Dashboard Admin</h2>
    <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Pantau pengguna dan statistik umum aplikasi Dompetku.</p>
</div>

<div class="row" style="margin-bottom: 24px; gap: 24px;">
    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-green"><i class="bi bi-people"></i></div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Pengguna</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $totalUsers }}</h3>
            <small style="color: var(--text-secondary);">Semua akun terdaftar</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-purple"><i class="bi bi-shield-check"></i></div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total Admin</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--primary);">{{ $totalAdmins }}</h3>
            <small style="color: var(--text-secondary);">Pengelola aplikasi</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon icon-green"><i class="bi bi-person"></i></div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Total User</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $totalRegularUsers }}</h3>
            <small style="color: var(--text-secondary);">Pengguna biasa</small>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex: 1 1 0;">
        <div class="card-modern">
            <div class="stat-icon" style="background: #EFF6FF; color: #2563EB;"><i class="bi bi-person-plus"></i></div>
            <h6 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">Pengguna Terbaru</h6>
            <h3 style="font-size: 28px; font-weight: 700; margin: 0; color: var(--text-primary);">{{ $recentUsers->count() }}</h3>
            <small style="color: var(--text-secondary);">8 terakhir terdaftar</small>
        </div>
    </div>
</div>

<div class="card-modern">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Pengguna Terbaru</h5>
            <small style="color: var(--text-secondary);">Daftar pengguna yang baru terdaftar</small>
        </div>
        <a href="{{ route('admin.users') }}" class="btn-modern btn-primary btn-sm">
            Lihat Semua Pengguna <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $u)
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
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 48px; color: var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                        Belum ada pengguna
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .row { gap: 16px; }
}
</style>
@endsection