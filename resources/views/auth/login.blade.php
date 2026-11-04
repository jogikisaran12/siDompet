@extends("layouts.app")

@section("title", "Login")

@section("content")

<div class="auth-layout">
    <div class="auth-banner">
        <div class="brand" style="border: none; padding: 0; margin-bottom: 48px;">
            <div class="brand-icon">
                <i class="bi bi-wallet2"></i>
            </div>
            Dompetku
        </div>

        <h1>Kelola uangmu.<br>Wujudkan tujuanmu.</h1>

        <p>Catat pemasukan, pantau pengeluaran, dan kelola keuangan pribadi dalam satu aplikasi yang sederhana dan elegan.</p>

        <div style="color: var(--text-primary); font-weight: 500;">
            <i class="bi bi-shield-check" style="color: var(--primary); margin-right: 8px;"></i>
            Aman, simpel, dan mudah digunakan
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-card">
            <h2 style="font-family: 'Poppins', sans-serif; font-size: 28px; font-weight: 700; margin: 0 0 4px; color: var(--text-primary);">Selamat Datang!</h2>
            <p style="color: var(--text-secondary); margin: 0 0 32px;">Masuk ke akun Dompetku untuk melanjutkan</p>

            @if($errors->any())
                <div class="alert-modern alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route("login") }}" method="POST">
                @csrf
                <div style="margin-bottom: 20px;">
                    <label class="label-modern">Alamat Email</label>
                    <input type="email" name="email" class="input-modern" placeholder="nama@email.com" value="{{ old("email") }}" required>
                </div>

                <div style="margin-bottom: 24px;">
                    <label class="label-modern">Password</label>
                    <input type="password" name="password" class="input-modern" placeholder="Masukkan password" required>
                </div>

                <button class="btn-modern btn-primary w-100" style="padding: 12px 18px;">
                    Masuk Sekarang
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">
                Belum memiliki akun?
                <a href="{{ route("register") }}" style="color: var(--primary); font-weight: 600;">Daftar Gratis</a>
            </p>
        </div>
    </div>
</div>

@endsection
