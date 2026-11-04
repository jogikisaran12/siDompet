<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoalEntry;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNum)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        // Tabungan periode
        $depositPeriod = SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('type', 'deposit')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->sum('amount');

        $withdrawalPeriod = SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('type', 'withdrawal')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->sum('amount');

        // Perubahan saldo tersedia periode
        $balancePeriodChange = $income - $expense - $depositPeriod + $withdrawalPeriod;

        $user = auth()->user();
        $availableBalanceNow = (float) $user->getAvailableBalance();
        $totalSavingsNow = (float) $user->getTotalSavingsBalance();
        $totalMoneyNow = (float) $user->getTotalMoney();

        // Tetap kirim balance untuk kompatibilitas view lama
        $balance = $balancePeriodChange;

        return view('reports.index', compact(
            'month',
            'transactions',
            'income',
            'expense',
            'balance',
            'depositPeriod',
            'withdrawalPeriod',
            'balancePeriodChange',
            'availableBalanceNow',
            'totalSavingsNow',
            'totalMoneyNow'
        ));
    }

    public function print(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $month = $request->input('month', now()->format('Y-m'));
        $year = substr($month, 0, 4);
        $monthNum = substr($month, 5, 2);

        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $monthNum)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        // Tabungan periode
        $depositPeriod = SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('type', 'deposit')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->sum('amount');

        $withdrawalPeriod = SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', auth()->id());
        })
            ->where('type', 'withdrawal')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->sum('amount');

        $balancePeriodChange = $income - $expense - $depositPeriod + $withdrawalPeriod;

        $user = auth()->user();
        $availableBalanceNow = (float) $user->getAvailableBalance();
        $totalSavingsNow = (float) $user->getTotalSavingsBalance();
        $totalMoneyNow = (float) $user->getTotalMoney();

        $balance = $balancePeriodChange;

        return view('reports.print', compact(
            'month',
            'transactions',
            'income',
            'expense',
            'balance',
            'depositPeriod',
            'withdrawalPeriod',
            'balancePeriodChange',
            'availableBalanceNow',
            'totalSavingsNow',
            'totalMoneyNow'
        ));
    }
}
