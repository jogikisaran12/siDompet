<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;

    public function categories()
{
    return $this->hasMany(Category::class);
}

public function transactions()
{
    return $this->hasMany(Transaction::class);
}

    public function savingsGoals()
    {
        return $this->hasMany(SavingsGoal::class);
    }

    public function getTotalIncome(): string
    {
        $total = $this->transactions()->where('type', 'income')->sum('amount');
        return $total === null ? '0.00' : number_format((float) $total, 2, '.', '');
    }

    public function getTotalExpense(): string
    {
        $total = $this->transactions()->where('type', 'expense')->sum('amount');
        return $total === null ? '0.00' : number_format((float) $total, 2, '.', '');
    }

    public function getTotalSavingsDeposits(): string
    {
        $total = \App\Models\SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', $this->id);
        })->where('type', 'deposit')->sum('amount');

        return $total === null ? '0.00' : number_format((float) $total, 2, '.', '');
    }

    public function getTotalSavingsWithdrawals(): string
    {
        $total = \App\Models\SavingsGoalEntry::whereHas('savingsGoal', function ($query) {
            $query->where('user_id', $this->id);
        })->where('type', 'withdrawal')->sum('amount');

        return $total === null ? '0.00' : number_format((float) $total, 2, '.', '');
    }

    public function getTotalSavingsBalance(): string
    {
        $deposits = $this->getTotalSavingsDeposits();
        $withdrawals = $this->getTotalSavingsWithdrawals();
        $balance = bcsub($deposits, $withdrawals, 2);
        return $balance;
    }

    public function getAvailableBalance(): string
    {
        $income = $this->getTotalIncome();
        $expense = $this->getTotalExpense();
        $deposits = $this->getTotalSavingsDeposits();
        $withdrawals = $this->getTotalSavingsWithdrawals();

        $balance = bcsub($income, $expense, 2);
        $balance = bcsub($balance, $deposits, 2);
        $balance = bcadd($balance, $withdrawals, 2);

        return $balance;
    }

    public function getTotalMoney(): string
    {
        $available = $this->getAvailableBalance();
        $savings = $this->getTotalSavingsBalance();
        $total = bcadd($available, $savings, 2);
        return $total;
    }
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
