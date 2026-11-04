<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Transaction::where('user_id', $user->id);

        // Total pemasukan dan pengeluaran (tetap ditampilkan seperti sebelumnya)
        $income = (float) $user->getTotalIncome();
        $expense = (float) $user->getTotalExpense();

        // Saldo terintegrasi menggunakan helper
        $availableBalance = (float) $user->getAvailableBalance();
        $totalSavingsBalance = (float) $user->getTotalSavingsBalance();
        $totalMoney = (float) $user->getTotalMoney();

        // Saldo tersedia untuk tampilan utama
        $balance = $availableBalance;

        // Lima transaksi terbaru
        $transactions = (clone $query)
            ->with('category')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Periode grafik: 12 bulan terakhir termasuk bulan ini
        $startDate = now()->startOfMonth()->subMonths(11);
        $endDate = now()->endOfMonth();

        // Ambil total transaksi per bulan.
        // Agregasi dilakukan di PHP (hanya kolom tanggal, jenis, nominal) agar query
        // tetap kompatibel dengan MySQL maupun SQLite. Hasil angkanya sama dengan
        // SUM(CASE WHEN ...) per bulan.
        $monthlyTransactions = (clone $query)
            ->whereBetween('transaction_date', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->get(['transaction_date', 'type', 'amount'])
            ->groupBy(function ($transaction) {
                return $transaction->transaction_date->format('Y-m');
            })
            ->map(function ($group) {
                return [
                    'income' => (float) $group->where('type', 'income')->sum('amount'),
                    'expense' => (float) $group->where('type', 'expense')->sum('amount'),
                ];
            });

        // Nama bulan
        $monthNames = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        $cursor = $startDate->copy();

        for ($i = 0; $i < 12; $i++) {
            $key = $cursor->format('Y-m');
            $monthNumber = (int) $cursor->format('n');

            $chartLabels[] = $monthNames[$monthNumber];

            $monthlyData = $monthlyTransactions->get($key);

            $chartIncome[] = (float) ($monthlyData['income'] ?? 0);
            $chartExpense[] = (float) ($monthlyData['expense'] ?? 0);

            $cursor->addMonth();
        }

        // Pengeluaran per kategori
        $expenseByCategory = Transaction::with('category')
            ->where('user_id', $user->id)
            ->where('type', 'expense')
            ->get()
            ->groupBy('category_id')
            ->map(function ($group) {
                $category = $group->first()->category;
                return [
                    'label' => $category ? $category->name : 'Tanpa Kategori',
                    'total' => (float) $group->sum('amount'),
                ];
            })
            ->values();

        $expenseCategoryLabels = $expenseByCategory->pluck('label')->toArray();
        $expenseCategoryData = $expenseByCategory->pluck('total')->toArray();

        // Kirim data lama dan data grafik ke Blade
        return view('dashboard', compact(
            'income',
            'expense',
            'balance',
            'availableBalance',
            'totalSavingsBalance',
            'totalMoney',
            'transactions',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'expenseCategoryLabels',
            'expenseCategoryData'
        ));
    }
}
