@extends("layouts.app")

@section("title", "Pengaturan - Dompetku")

@section("content")

<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-family: \"Poppins\", sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Pengaturan</h2>
        <p style="color: var(--text-secondary); margin: 0; font-size: 14px;">Kelola profil dan preferensi akun Dompetku Anda.</p>
    </div>
    <div style="text-align: right;">
        <small style="color: var(--text-secondary);">{{ auth()->user()->name }} - Pengguna Dompetku</small>
    </div>
</div>

@if(session("success"))
<div class="alert-modern alert-success"><i class="bi bi-check-circle me-2"></i>{{ session("success") }}</div>
@endif

@if($errors->any())
<div class="alert-modern alert-danger"><ul style="margin: 0; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<div class="row" style="margin-bottom: 24px; gap: 24px;">
    <div class="col-lg-6 col-md-12" style="flex: 1 1 0;">
        <div class="card-modern">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Profil Akun</h5>
                    <small style="color: var(--text-secondary);">Perbarui informasi profil Anda</small>
                </div>
            </div>

            <form action="{{ route('settings.update') }}" method="POST" style="max-width: 500px;">
                @csrf

                <div style="margin-bottom: 20px;">
                    <label for="name" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary);">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="input-modern" placeholder="Masukkan nama lengkap" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="email" style="display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--text-primary);">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="input-modern" placeholder="Masukkan email" required>
                </div>

                <button type="submit" class="btn-modern btn-primary">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-6 col-md-12" style="flex: 1 1 0;">
        <div class="card-modern">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                    <h5 style="font-size: 18px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Informasi Akun</h5>
                    <small style="color: var(--text-secondary);">Detail akun Dompetku Anda</small>
                </div>
            </div>

            <div style="display: grid; gap: 16px;">
                <div style="display: flex; justify-content: space-between; padding: 16px 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary); font-size: 14px;">Nama Pengguna</span>
                    <span style="font-weight: 600; color: var(--text-primary);">{{ $user->name }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 16px 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary); font-size: 14px;">Email</span>
                    <span style="font-weight: 600; color: var(--text-primary);">{{ $user->email }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 16px 0; border-bottom: 1px solid var(--border-color);">
                    <span style="color: var(--text-secondary); font-size: 14px;">Tanggal Daftar</span>
                    <span style="font-weight: 600; color: var(--text-primary);">{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->locale('id')->translatedFormat('d F Y') : '-' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 16px 0;">
                    <span style="color: var(--text-secondary); font-size: 14px;">Aplikasi</span>
                    <span style="font-weight: 600; color: var(--text-primary);">Dompetku v1.0</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection