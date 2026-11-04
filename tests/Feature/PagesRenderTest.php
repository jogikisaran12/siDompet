<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian render seluruh halaman Dompetku.
 *
 * Seluruh pengujian berjalan pada database testing SQLite in-memory
 * (lihat tests/TestCase.php) memakai data pengguna dummy dari factory,
 * sehingga database utama aplikasi tidak pernah disentuh.
 */
class PagesRenderTest extends TestCase
{
    use RefreshDatabase;

    private function userWithDummyData(): User
    {
        $user = User::factory()->create(['name' => 'Pengguna Uji']);

        $expenseCategory = $user->categories()->create([
            'name' => 'Makanan Uji',
            'type' => 'expense',
        ]);

        $incomeCategory = $user->categories()->create([
            'name' => 'Gaji Uji',
            'type' => 'income',
        ]);

        $user->transactions()->create([
            'category_id' => $expenseCategory->id,
            'type' => 'expense',
            'amount' => 25000,
            'description' => 'Makan siang uji',
            'transaction_date' => now()->toDateString(),
        ]);

        $user->transactions()->create([
            'category_id' => $incomeCategory->id,
            'type' => 'income',
            'amount' => 5000000,
            'description' => 'Gaji uji',
            'transaction_date' => now()->startOfMonth()->toDateString(),
        ]);

        return $user;
    }

    public function test_halaman_publik_login_dan_register_tampil(): void
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('Selamat Datang!')
            ->assertSee('themeToggle', false);

        $this->get('/register')
            ->assertStatus(200)
            ->assertSee('Buat Akun Baru')
            ->assertSee('themeToggle', false);
    }

    public function test_halaman_dashboard_tampil_dengan_grafik_yang_valid(): void
    {
        $user = $this->userWithDummyData();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('const labels =', false);
        $response->assertSee('const incomeData =', false);
        $response->assertSee('const expenseData =', false);
        $response->assertSee('Saldo Tersedia');
        $response->assertSee('Pengguna Uji');

        // Grafik harus berisi 12 label bulan
        $this->assertMatchesRegularExpression(
            '/const labels = \[(?:"[^"]+",?){12}\]/',
            $response->getContent()
        );

        // Nominal transaksi dummy harus masuk ke data grafik
        $response->assertSee('5000000', false);
        $response->assertSee('25000', false);
    }

    public function test_halaman_transaksi_tampil_dengan_data_dummy(): void
    {
        $user = $this->userWithDummyData();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertSee('Makan siang uji');
        $response->assertSee('Transaksi');
    }

    public function test_halaman_form_transaksi_tampil(): void
    {
        $user = $this->userWithDummyData();

        $this->actingAs($user)->get('/transactions/create')->assertStatus(200);

        $transaction = $user->transactions()->first();

        $this->actingAs($user)->get('/transactions/' . $transaction->id . '/edit')
            ->assertStatus(200)
            ->assertSee('Makan siang uji');
    }

    public function test_halaman_kategori_tampil(): void
    {
        $user = $this->userWithDummyData();

        $this->actingAs($user)->get('/categories')
            ->assertStatus(200)
            ->assertSee('Makanan Uji');
    }

    public function test_halaman_laporan_dan_cetak_tampil(): void
    {
        $user = $this->userWithDummyData();

        $this->actingAs($user)->get('/reports')
            ->assertStatus(200)
            ->assertSee('Ringkasan Keuangan');

        $this->actingAs($user)->get('/reports/print')
            ->assertStatus(200)
            ->assertSee('LAPORAN KEUANGAN DOMPETKU');
    }

    public function test_halaman_pengaturan_tampil(): void
    {
        $user = $this->userWithDummyData();

        $this->actingAs($user)->get('/settings')
            ->assertStatus(200)
            ->assertSee('Pengguna Uji')
            ->assertSee('Profil Akun');
    }

    public function test_setiap_halaman_memiliki_tombol_tema_dan_tanpa_penyembunyian_body(): void
    {
        $user = $this->userWithDummyData();

        $urls = [
            '/dashboard',
            '/transactions',
            '/transactions/create',
            '/categories',
            '/reports',
            '/settings',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)->get($url);

            $response->assertStatus(200);
            $response->assertSee('id="themeToggle"', false);
            $response->assertSee('js/theme.js', false);
            $response->assertDontSee('visibility: hidden', false);
        }
    }
}
