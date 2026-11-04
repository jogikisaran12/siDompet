@extends("layouts.app")

@section("title", "Daftar")

@section("content")

<div class="auth-layout">
    <div class="auth-banner">
        <div class="brand" style="border: none; padding: 0; margin-bottom: 48px;">
            <div class="brand-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            Dompetku
        </div>

        <h1>Mulai catat<br>keuanganmu sekarang</h1>

        <p>Buat akun gratis dan mulai kelola keuangan pribadi dengan lebih teratur dan bijak.</p>

        <div style="color: var(--text-primary); font-weight: 500;">
            <i class="bi bi-check-circle" style="color: var(--primary); margin-right: 8px;"></i>
            Gratis selamanya
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-card">
            <h2 style="font-family: \'Poppins\', sans-serif; font-size: 28px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Buat Akun Baru</h2>
            <p style="color: var(--text-secondary); margin: 0 0 32px;">Daftar untuk mulai menggunakan Dompetku</p>

            @if($errors->any())
                <div class="alert-modern alert-danger">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route("register") }}" method="POST">
                @csrf
                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Nama Lengkap</label>
                    <input type="text" name="name" class="input-modern" placeholder="Masukkan nama lengkap" value="{{ old("name") }}" required>
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Alamat Email</label>
                    <input type="email" name="email" class="input-modern" placeholder="nama@email.com" value="{{ old("email") }}" required>
                </div>

                <div style="margin-bottom: 16px;">
                    <label class="label-modern">Password</label>
                    <input type="password" name="password" class="input-modern" placeholder="Minimal 8 karakter" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="label-modern">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input-modern" placeholder="Ulangi password" required>
                </div>

                <button class="btn-modern btn-primary w-100" style="padding: 12px 18px;">
                    Daftar Sekarang
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
                Sudah memiliki akun?
                <a href="{{ route("login") }}" style="color: var(--primary); font-weight: 600;">Masuk</a>
            </p>
        </div>
    </div>
</div>

@endsection
