<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_tampil_lengkap_dengan_tombol_tema(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Selamat Datang!');
        $response->assertSee('id="themeToggle"', false);
        $response->assertSee('js/theme.js', false);
    }

    public function test_halaman_register_tampil_lengkap_dengan_tombol_tema(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Buat Akun Baru');
        $response->assertSee('id="themeToggle"', false);
        $response->assertSee('js/theme.js', false);
    }

    public function test_script_tema_dimuat_di_head_sebelum_body_dirender(): void
    {
        $html = $this->get('/login')->getContent();

        $scriptPosition = strpos($html, 'js/theme.js');
        $bodyPosition = strpos($html, '<body');

        $this->assertNotFalse($scriptPosition, 'Script tema harus ikut dimuat.');
        $this->assertNotFalse($bodyPosition);
        $this->assertLessThan(
            $bodyPosition,
            $scriptPosition,
            'Script tema harus berada di dalam <head> agar tidak ada efek berkedip.'
        );
    }

    public function test_css_tidak_menyembunyikan_body_agar_halaman_tidak_kosong(): void
    {
        $css = file_get_contents(public_path('css/dompetku.css'));

        $this->assertStringNotContainsString('visibility: hidden', $css);
        $this->assertStringNotContainsString('dark-theme-ready', $css);
    }

    public function test_tema_default_adalah_terang(): void
    {
        $html = $this->get('/login')->getContent();

        $this->assertStringContainsString('<html lang="id">', $html);
    }

    public function test_theme_js_menyimpan_preferensi_tema_di_localstorage(): void
    {
        $path = public_path('js/theme.js');

        $this->assertFileExists($path);

        $contents = file_get_contents($path);

        $this->assertStringContainsString('var STORAGE_KEY = "dompetku-theme"', $contents);
        $this->assertStringContainsString('localStorage.setItem(STORAGE_KEY, theme)', $contents);
        $this->assertStringContainsString('readStoredTheme() === "dark" ? "dark" : "light"', $contents);
    }

    public function test_css_menyediakan_variabel_tema_gelap(): void
    {
        $css = file_get_contents(public_path('css/dompetku.css'));

        $this->assertStringContainsString('.dark {', $css);
        $this->assertStringContainsString('--bg-primary: #0F172A', $css);
        $this->assertStringContainsString('.dark .theme-icon-sun', $css);
        $this->assertStringContainsString('.dark .theme-icon-moon', $css);
    }

    public function test_dashboard_mendefinisikan_variabel_grafik_dengan_benar(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('const labels =', false);
        $response->assertSee('const incomeData =', false);
        $response->assertSee('const expenseData =', false);
    }

    public function test_halaman_terlindungi_tetapa_memiliki_tombol_tema(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertSee('id="themeToggle"', false);
        $response->assertSee('js/theme.js', false);
    }

    public function test_tamu_tidak_bisa_membuka_halaman_terlindungi(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/transactions')->assertRedirect('/login');
        $this->get('/categories')->assertRedirect('/login');
        $this->get('/reports')->assertRedirect('/login');
        $this->get('/settings')->assertRedirect('/login');
    }

    public function test_halaman_laporan_cetak_juga_mendukung_tema(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/reports/print');

        $response->assertStatus(200);
        $response->assertSee('js/theme.js', false);
        $response->assertSee('.dark body', false);
    }
}
