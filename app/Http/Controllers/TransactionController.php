<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:income,expense',
            'month' => 'nullable|date_format:Y-m',
        ]);

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->when($request->month, function ($query, $month) {
                $query->whereYear('transaction_date', substr($month, 0, 4))
                    ->whereMonth('transaction_date', substr($month, 5, 2));
            })
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->get();

        return view('transactions.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $amountStr = bcmul((string) $data['amount'], '1', 2);

        try {
            DB::transaction(function () use ($data, $amountStr) {
                $user = User::where('id', auth()->id())->lockForUpdate()->firstOrFail();

                $availableBalanceStr = $user->getAvailableBalance();

                if ($data['type'] === 'expense') {
                    if (bccomp($amountStr, $availableBalanceStr, 2) > 0) {
                        $availableFormatted = 'Rp ' . number_format((float) $availableBalanceStr, 0, ',', '.');
                        $shortfall = bcsub($amountStr, $availableBalanceStr, 2);
                        $shortfallFormatted = 'Rp ' . number_format((float) $shortfall, 0, ',', '.');

                        throw ValidationException::withMessages([
                            'amount' => "Saldo tidak mencukupi. Saldo tersedia {$availableFormatted}. Kekurangan {$shortfallFormatted}.",
                        ]);
                    }
                }

                $user->transactions()->create([
                    'category_id' => $data['category_id'],
                    'type' => $data['type'],
                    'amount' => $amountStr,
                    'description' => $data['description'] ?? null,
                    'transaction_date' => $data['transaction_date'],
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeOwner($transaction);

        $categories = Category::where('user_id', auth()->id())->get();

        return view('transactions.form', compact(
            'transaction',
            'categories'
        ));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeOwner($transaction);

        $data = $this->validatedData($request);
        $newAmountStr = bcmul((string) $data['amount'], '1', 2);
        $oldAmountStr = bcmul((string) $transaction->amount, '1', 2);

        try {
            DB::transaction(function () use ($transaction, $data, $newAmountStr, $oldAmountStr) {
                $user = User::where('id', auth()->id())->lockForUpdate()->firstOrFail();

                $lockedTransaction = Transaction::where('id', $transaction->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $availableBefore = $user->getAvailableBalance();

                $projected = $availableBefore;

                if ($lockedTransaction->type === 'income') {
                    $projected = bcsub($projected, $oldAmountStr, 2);
                } elseif ($lockedTransaction->type === 'expense') {
                    $projected = bcadd($projected, $oldAmountStr, 2);
                }

                if ($data['type'] === 'income') {
                    $projected = bcadd($projected, $newAmountStr, 2);
                } elseif ($data['type'] === 'expense') {
                    if (bccomp($newAmountStr, $projected, 2) > 0) {
                        $availableFormatted = 'Rp ' . number_format((float) $projected, 0, ',', '.');
                        $shortfall = bcsub($newAmountStr, $projected, 2);
                        $shortfallFormatted = 'Rp ' . number_format((float) $shortfall, 0, ',', '.');

                        throw ValidationException::withMessages([
                            'amount' => "Saldo tidak mencukupi. Saldo tersedia {$availableFormatted}. Kekurangan {$shortfallFormatted}.",
                        ]);
                    }
                    $projected = bcsub($projected, $newAmountStr, 2);
                }

                $lockedTransaction->update([
                    'category_id' => $data['category_id'],
                    'type' => $data['type'],
                    'amount' => $newAmountStr,
                    'description' => $data['description'] ?? null,
                    'transaction_date' => $data['transaction_date'],
                ]);
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeOwner($transaction);

        $amountStr = bcmul((string) $transaction->amount, '1', 2);

        try {
            DB::transaction(function () use ($transaction, $amountStr) {
                $user = User::where('id', auth()->id())->lockForUpdate()->firstOrFail();

                $lockedTransaction = Transaction::where('id', $transaction->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $availableBefore = $user->getAvailableBalance();

                if ($lockedTransaction->type === 'income') {
                    if (bccomp($amountStr, $availableBefore, 2) > 0) {
                        $availableFormatted = 'Rp ' . number_format((float) $availableBefore, 0, ',', '.');
                        $shortfall = bcsub($amountStr, $availableBefore, 2);
                        $shortfallFormatted = 'Rp ' . number_format((float) $shortfall, 0, ',', '.');

                        throw ValidationException::withMessages([
                            'amount' => "Penghapusan tidak diperbolehkan. Saldo tidak mencukupi. Saldo tersedia {$availableFormatted}. Kekurangan {$shortfallFormatted}.",
                        ]);
                    }
                }

                $lockedTransaction->delete();
            });
        } catch (ValidationException $e) {
            throw $e;
        }

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    private function authorizeOwner(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === auth()->id(), 403);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', auth()->id())
                    ->where('type', $request->type)
            ],
            'amount' => 'required|numeric|gt:0|max:9999999999999.99',
            'description' => 'nullable|string|max:1000',
            'transaction_date' => 'required|date'
        ]);
    }
}
