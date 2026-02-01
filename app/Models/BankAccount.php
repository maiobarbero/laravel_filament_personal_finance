<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\BelongsToUser;

class BankAccount extends Model
{
    /** @use HasFactory<\Database\Factories\BankAccountFactory> */
    use HasFactory;
    use BelongsToUser;

    protected $fillable = [
        'user_id',
        'name',
        'balance',
    ];

    protected function casts(): array
    {
        return [
            'balance' => \App\Casts\MoneyCast::class,
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
