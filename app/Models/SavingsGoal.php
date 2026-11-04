<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsGoal extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'target_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'target_amount' => 'decimal:2',
            'target_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SavingsGoalEntry::class);
    }
}
