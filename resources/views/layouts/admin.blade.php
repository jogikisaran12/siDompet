<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10B981">
    <title>@yield("title", "Admin") - Dompetku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset("css/dompetku.css") }}" rel="stylesheet">
    <script src="{{ asset("js/theme.js") }}"></script>
</head>
<body>
<div class="app-layout">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-shield-check"></i></div>
            Dompetku Admin
        </div>
        <p class="menu-label">ADMIN</p>
        <nav>
            <a href="{{ route("admin.dashboard") }}" class="sidebar-menu {{ request()->routeIs("admin.dashboard") ? "active" : "" }}"><i class="bi bi-grid-1x2"></i><span>Dashboard</span></a>
            <a href="{{ route("admin.users") }}" class="sidebar-menu {{ request()->routeIs("admin.users*") ? "active" : "" }}"><i class="bi bi-people"></i><span>Manajemen Pengguna</span></a>
            <a href="{{ route("admin.admins") }}" class="sidebar-menu {{ request()->routeIs("admin.admins*") ? "active" : "" }}"><i class="bi bi-shield-check"></i><span>Manajemen Admin</span></a>
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
                    <h1 class="page-title">@yield("title", "Dashboard Admin")</h1>
                    <p class="page-subtitle">Panel pengelolaan aplikasi Dompetku</p>
                </div>
            </div>
            <div class="right-section">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold" style="color: var(--text-primary);">{{ auth()->user()->name }}</div>
                    <small style="color: var(--text-secondary);">Administrator</small>
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
<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebar = document.getElementById("sidebar");
    const toggle = document.getElementById("sidebarToggle");
    if (!sidebar || !toggle) return;
    toggle.style.display = "inline-flex";
    toggle.addEventListener("click", function() { sidebar.classList.toggle("open"); });
    document.addEventListener("click", function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !toggle.contains(e.target)) sidebar.classList.remove("open");
        }
    });
});
</script>
</body>
</html>
