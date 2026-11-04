<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10B981">
    <title>@yield("title", "Dashboard") - Dompetku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset("css/dompetku.css") }}" rel="stylesheet">
    {{-- Tema diterapkan sebelum halaman digambar agar tidak ada efek berkedip --}}
    <script src="{{ asset("js/theme.js") }}"></script>
</head>
<body>
@auth
<div class="app-layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-wallet2"></i></div>
            Dompetku
        </div>
        <p class="menu-label">MENU UTAMA</p>
        <nav>
            <a href="{{ route("dashboard") }}" class="sidebar-menu {{ request()->routeIs("dashboard") ? "active" : "" }}"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
            <a href="{{ route("transactions.index") }}" class="sidebar-menu {{ request()->routeIs("transactions.*") ? "active" : "" }}"><i class="bi bi-arrow-left-right"></i><span>Transaksi</span></a>
            <a href="{{ route("savings-goals.index") }}" class="sidebar-menu {{ request()->routeIs("savings-goals.*") ? "active" : "" }}"><i class="bi bi-piggy-bank"></i><span>Target Tabungan</span></a>
            <a href="{{ route("categories.index") }}" class="sidebar-menu {{ request()->routeIs("categories.*") ? "active" : "" }}"><i class="bi bi-collection"></i><span>Kategori</span></a>
            <a href="{{ route("reports.index") }}" class="sidebar-menu {{ request()->routeIs("reports.*") ? "active" : "" }}"><i class="bi bi-bar-chart-line"></i><span>Laporan Keuangan</span></a>
            <a href="{{ route("settings.index") }}" class="sidebar-menu {{ request()->routeIs("settings.*") ? "active" : "" }}"><i class="bi bi-gear"></i><span>Pengaturan</span></a>
        </nav>
        <div class="sidebar-bottom">
            <div class="sidebar-user">
                <small>Login sebagai</small>
                <div class="user-name">{{ auth()->user()->name }}</div>
            </div>
            <form action="{{ route("logout") }}" method="POST">@csrf<button type="submit" class="sidebar-menu w-100" style="background: transparent; text-align: left;"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button></form>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div class="left-section">
                <button class="btn-modern btn-secondary btn-sm" id="sidebarToggle" style="display: none;"><i class="bi bi-list"></i></button>
                <div>
                    <h1 class="page-title">@yield("title", "Dashboard")</h1>
                    <p class="page-subtitle">Kelola keuanganmu dengan lebih mudah</p>
                </div>
            </div>
            <div class="right-section">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold" style="color: var(--text-primary);">{{ auth()->user()->name }}</div>
                    <small style="color: var(--text-secondary);">Pengguna Dompetku</small>
                </div>
                <button id="themeToggle" class="btn-modern btn-outline btn-sm" title="Ganti Tema" aria-label="Ganti Tema">
                    <i class="bi bi-sun-fill theme-icon-sun"></i>
                    <i class="bi bi-moon-fill theme-icon-moon"></i>
                </button>
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            </div>
        </header>
        @if(session("success"))<div class="alert-modern alert-success"><i class="bi bi-check-circle me-2"></i>{{ session("success") }}</div>@endif
        @if($errors->any())<div class="alert-modern alert-danger"><ul style="margin: 0; padding-left: 20px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @yield("content")
    </main>
</div>
@else
    {{-- Halaman tamu (login / register) tetap bisa berganti tema --}}
    <div class="auth-theme-bar">
        <button id="themeToggle" class="btn-modern btn-outline btn-sm" title="Ganti Tema" aria-label="Ganti Tema">
            <i class="bi bi-sun-fill theme-icon-sun"></i>
            <i class="bi bi-moon-fill theme-icon-moon"></i>
        </button>
    </div>
    @yield("content")
@endauth
<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");

    if (!sidebar || !toggle) {
        return;
    }

    toggle.style.display = "inline-flex";
    toggle.addEventListener("click", function() { sidebar.classList.toggle("open"); });

    document.addEventListener("click", function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove("open");
            }
        }
    });
});
</script>
</body>
</html>
