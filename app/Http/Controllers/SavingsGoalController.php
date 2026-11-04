<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use App\Models\SavingsGoalEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $goals = auth()->user()
            ->savingsGoals()
            ->with('entries')
            ->latest()
            ->get()
            ->map(function ($goal) {
                $goal->balance = $this->calculateBalance($goal);
                $goal->progress = $this->calculateProgress($goal, $goal->balance);
                $goal->progressDisplay = min($goal->progress, 100);
                return $goal;
            });

        return view('savings-goals.index', compact('goals'));
    }

    public function create()
    {
        return view('savings-goals.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|gt:0',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $request->user()->savingsGoals()->create($validated);

        return redirect()->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil dibuat.');
    }

    public function show(SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        $savingsGoal->load('entries');
        $balance = $this->calculateBalance($savingsGoal);
        $progress = $this->calculateProgress($savingsGoal, $balance);
        $progressDisplay = min($progress, 100);

        $entries = $savingsGoal->entries()->latest()->get();

        return view('savings-goals.show', compact('savingsGoal', 'balance', 'progress', 'progressDisplay', 'entries'));
    }

    public function edit(SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        return view('savings-goals.form', compact('savingsGoal'));
    }

    public function update(Request $request, SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|gt:0',
            'target_date' => 'nullable|date',
            'description' => 'nullable|string|max:1000',
        ]);

        $savingsGoal->update($validated);

        return redirect()->route('savings-goals.show', $savingsGoal)
            ->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function destroy(SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        $savingsGoal->delete();

        return redirect()->route('savings-goals.index')
            ->with('success', 'Target tabungan berhasil dihapus.');
    }

    public function addDeposit(Request $request, SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $amountInput = $validated['amount'];
        $amountStr = number_format((float) $amountInput, 2, '.', '');

        try {
            DB::transaction(function () use ($savingsGoal, $amountStr, $validated) {
                // Kunci user terlebih dahulu
                $user = User::where('id', auth()->id())->lockForUpdate()->firstOrFail();

                // Kunci target tabungan milik pengguna
                $goal = SavingsGoal::where('id', $savingsGoal->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Hitung ulang saldo tersedia setelah lock
                $availableBalanceStr = $user->getAvailableBalance();

                // Bandingkan menggunakan BCMath
                if (bccomp($amountStr, $availableBalanceStr, 2) > 0) {
                    $availableFormatted = 'Rp ' . number_format((float) $availableBalanceStr, 0, ',', '.');
                    $amountFormatted = 'Rp ' . number_format((float) $amountStr, 0, ',', '.');
                    $shortfall = bcsub($amountStr, $availableBalanceStr, 2);
                    $shortfallFormatted = 'Rp ' . number_format((float) $shortfall, 0, ',', '.');

                    throw ValidationException::withMessages([
                        'amount' => "Saldo tidak mencukupi. Saldo tersedia {$availableFormatted}. Kekurangan {$shortfallFormatted}.",
                    ]);
                }

                $goal->entries()->create([
                    'type' => 'deposit',
                    'amount' => $amountStr,
                    'note' => $validated['note'] ?? null,
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('savings-goals.show', $savingsGoal)
            ->with('success', 'Setoran berhasil ditambahkan.');
    }

    public function addWithdrawal(Request $request, SavingsGoal $savingsGoal)
    {
        $this->authorizeOwner($savingsGoal);

        $validated = $request->validate([
            'amount' => 'required|numeric|gt:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $amountStr = number_format((float) $validated['amount'], 2, '.', '');

        try {
            DB::transaction(function () use ($savingsGoal, $amountStr, $validated) {
                // Kunci user terlebih dahulu untuk serialisasi
                $user = User::where('id', auth()->id())->lockForUpdate()->firstOrFail();

                // Lock the goal row for update
                $goal = SavingsGoal::where('id', $savingsGoal->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Calculate balance after lock
                $balance = $this->calculateBalanceForGoal($goal);

                // Check if withdrawal amount exceeds balance
                if (bccomp($amountStr, $balance, 2) > 0) {
                    throw ValidationException::withMessages([
                        'amount' => 'Penarikan tidak boleh melebihi saldo tabungan.',
                    ]);
                }

                // Create withdrawal entry atomically
                $goal->entries()->create([
                    'type' => 'withdrawal',
                    'amount' => $amountStr,
                    'note' => $validated['note'] ?? null,
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('savings-goals.show', $savingsGoal)
            ->with('success', 'Penarikan berhasil dilakukan.');
    }

    private function authorizeOwner(SavingsGoal $savingsGoal): void
    {
        abort_unless($savingsGoal->user_id === auth()->id(), 403);
    }

    private function calculateBalance(SavingsGoal $goal)
    {
        return $this->calculateBalanceForGoal($goal);
    }

    private function calculateBalanceForGoal(SavingsGoal $goal)
    {
        $deposits = $goal->entries()->where('type', 'deposit')->sum('amount');
        $withdrawals = $goal->entries()->where('type', 'withdrawal')->sum('amount');

        $balance = bcsub((string)$deposits, (string)$withdrawals, 2);
        return $balance;
    }

    private function calculateProgress(SavingsGoal $goal, $balance)
    {
        $target = (string)$goal->target_amount;
        if (bccomp($target, '0', 2) <= 0) {
            return 0;
        }
        $progress = bcmul(bcdiv($balance, $target, 4), '100', 2);
        return (float)$progress;
    }
}
