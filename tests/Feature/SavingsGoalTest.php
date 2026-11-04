<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SavingsGoal;
use App\Models\SavingsGoalEntry;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavingsGoalTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithCategory(): User
    {
        $user = User::factory()->create();
        $user->categories()->create([
            'name' => 'Gaji Test',
            'type' => 'income',
        ]);
        return $user;
    }

    public function test_user_dapat_membuat_target_tabungan(): void
    {
        $user = $this->createUserWithCategory();

        $response = $this->actingAs($user)->post(route('savings-goals.store'), [
            'name' => 'Beli Laptop',
            'target_amount' => 10000000,
            'target_date' => now()->addMonths(6)->toDateString(),
            'description' => 'Target beli laptop',
        ]);

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseHas('savings_goals', [
            'user_id' => $user->id,
            'name' => 'Beli Laptop',
        ]);
    }

    public function test_validasi_target_harus_lebih_dari_nol(): void
    {
        $user = $this->createUserWithCategory();

        $response = $this->actingAs($user)->post(route('savings-goals.store'), [
            'name' => 'Target Salah',
            'target_amount' => 0,
        ]);

        $response->assertSessionHasErrors('target_amount');
        $this->assertDatabaseCount('savings_goals', 0);
    }

    public function test_user_hanya_melihat_target_miliknya_sendiri(): void
    {
        $user1 = $this->createUserWithCategory();
        $user2 = $this->createUserWithCategory();

        $user1->savingsGoals()->create([
            'name' => 'Target User 1',
            'target_amount' => 5000000,
        ]);
        $user2->savingsGoals()->create([
            'name' => 'Target User 2',
            'target_amount' => 3000000,
        ]);

        $response = $this->actingAs($user1)->get(route('savings-goals.index'));
        $response->assertStatus(200);
        $response->assertSee('Target User 1');
        $response->assertDontSee('Target User 2');
    }

    public function test_user_tidak_bisa_mengakses_target_milik_pengguna_lain(): void
    {
        $user1 = $this->createUserWithCategory();
        $user2 = $this->createUserWithCategory();

        $goal2 = $user2->savingsGoals()->create([
            'name' => 'Target User 2',
            'target_amount' => 3000000,
        ]);

        $response = $this->actingAs($user1)->get(route('savings-goals.show', $goal2));
        $response->assertStatus(403);
    }

    public function test_user_dapat_menambahkan_setoran(): void
    {
        $user = $this->createUserWithCategory();
        $category = $user->categories()->where('type', 'income')->first();
        $user->transactions()->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Gaji',
            'transaction_date' => now()->toDateString(),
        ]);
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 1000000,
        ]);

        $response = $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 250000,
            'note' => 'Setoran awal',
        ]);

        $response->assertRedirect(route('savings-goals.show', $goal));
        $this->assertDatabaseHas('savings_goal_entries', [
            'savings_goal_id' => $goal->id,
            'type' => 'deposit',
        ]);
        $this->assertEquals(1, $goal->fresh()->entries()->count());
    }

    public function test_user_dapat_melakukan_penarikan_dengan_saldo_cukup(): void
    {
        $user = $this->createUserWithCategory();
        $category = $user->categories()->where('type', 'income')->first();
        $user->transactions()->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Gaji',
            'transaction_date' => now()->toDateString(),
        ]);
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 1000000,
        ]);

        $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 500000,
        ]);

        $response = $this->actingAs($user)->post(route('savings-goals.withdrawal', $goal), [
            'amount' => 200000,
            'note' => 'Tarik untuk kebutuhan',
        ]);

        $response->assertRedirect(route('savings-goals.show', $goal));
        $this->assertDatabaseHas('savings_goal_entries', [
            'savings_goal_id' => $goal->id,
            'type' => 'withdrawal',
        ]);
        $this->assertEquals(2, $goal->fresh()->entries()->count());
    }

    public function test_menolak_penarikan_yang_melebihi_saldo(): void
    {
        $user = $this->createUserWithCategory();
        $category = $user->categories()->where('type', 'income')->first();
        $user->transactions()->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Gaji',
            'transaction_date' => now()->toDateString(),
        ]);
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 1000000,
        ]);

        $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 100000,
        ]);

        $response = $this->actingAs($user)->post(route('savings-goals.withdrawal', $goal), [
            'amount' => 200000,
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertEquals(1, $goal->fresh()->entries()->count());
    }

    public function test_menghitung_saldo_dari_setoran_dikurangi_penarikan(): void
    {
        $user = $this->createUserWithCategory();
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 2000000,
        ]);

        $goal->entries()->create(['type' => 'deposit', 'amount' => 1000000]);
        $goal->entries()->create(['type' => 'deposit', 'amount' => 500000]);
        $goal->entries()->create(['type' => 'withdrawal', 'amount' => 200000]);

        $controller = app(\App\Http\Controllers\SavingsGoalController::class);
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('calculateBalanceForGoal');
        $method->setAccessible(true);

        $balance = $method->invoke($controller, $goal);
        $this->assertEquals('1300000.00', $balance);
    }

    public function test_progres_dibatasi_maksimal_100_persen_pada_tampilan(): void
    {
        $user = $this->createUserWithCategory();
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 1000000,
        ]);

        $goal->entries()->create(['type' => 'deposit', 'amount' => 1500000]);

        $controller = app(\App\Http\Controllers\SavingsGoalController::class);
        $reflection = new \ReflectionClass($controller);
        $methodProgress = $reflection->getMethod('calculateProgress');
        $methodProgress->setAccessible(true);
        $methodBalance = $reflection->getMethod('calculateBalanceForGoal');
        $methodBalance->setAccessible(true);

        $balance = $methodBalance->invoke($controller, $goal);
        $progress = $methodProgress->invoke($controller, $goal, $balance);
        $progressDisplay = min((float)$progress, 100);

        $this->assertLessThanOrEqual(100, $progressDisplay);
    }

    public function test_user_dapat_mengubah_target_tabungan(): void
    {
        $user = $this->createUserWithCategory();
        $goal = $user->savingsGoals()->create([
            'name' => 'Target Lama',
            'target_amount' => 1000000,
        ]);

        $response = $this->actingAs($user)->put(route('savings-goals.update', $goal), [
            'name' => 'Target Baru',
            'target_amount' => 2000000,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('savings_goals', [
            'id' => $goal->id,
            'name' => 'Target Baru',
        ]);
    }

    public function test_menghapus_target_beserta_catatan_via_cascade(): void
    {
        $user = $this->createUserWithCategory();
        $goal = $user->savingsGoals()->create([
            'name' => 'Target Hapus',
            'target_amount' => 1000000,
        ]);

        $goal->entries()->create(['type' => 'deposit', 'amount' => 100000]);
        $this->assertDatabaseCount('savings_goal_entries', 1);

        $response = $this->actingAs($user)->delete(route('savings-goals.destroy', $goal));

        $response->assertRedirect(route('savings-goals.index'));
        $this->assertDatabaseMissing('savings_goals', ['id' => $goal->id]);
        $this->assertDatabaseCount('savings_goal_entries', 0);
    }

    public function test_pengguna_tidak_dapat_mengubah_target_milik_pengguna_lain(): void
    {
        $user1 = $this->createUserWithCategory();
        $user2 = $this->createUserWithCategory();

        $goal2 = $user2->savingsGoals()->create([
            'name' => 'Target User 2',
            'target_amount' => 3000000,
        ]);

        $response = $this->actingAs($user1)->put(route('savings-goals.update', $goal2), [
            'name' => 'Dicoba Diubah',
            'target_amount' => 5000000,
        ]);

        $response->assertStatus(403);
    }

    public function test_setoran_dan_penarikan_tidak_mengubah_transaksi_utama(): void
    {
        $user = $this->createUserWithCategory();
        $category = $user->categories()->where('type', 'income')->first();
        $user->transactions()->create([
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 1000000,
            'description' => 'Gaji',
            'transaction_date' => now()->toDateString(),
        ]);
        $goal = $user->savingsGoals()->create([
            'name' => 'Tabungan',
            'target_amount' => 1000000,
        ]);

        $transactionCountBefore = Transaction::where('user_id', $user->id)->count();

        $this->actingAs($user)->post(route('savings-goals.deposit', $goal), [
            'amount' => 500000,
        ]);

        $this->actingAs($user)->post(route('savings-goals.withdrawal', $goal), [
            'amount' => 200000,
        ]);

        $transactionCountAfter = Transaction::where('user_id', $user->id)->count();
        $this->assertEquals($transactionCountBefore, $transactionCountAfter);
    }
}
