<?php

namespace App\Models;

use App\Enums\BudgetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToUser;

class Budget extends Model
{
    /** @use HasFactory<\Database\Factories\BudgetFactory> */
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'amount',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => BudgetType::class,
            'amount' => \App\Casts\MoneyCast::class,
        ];
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
