<?php

namespace App\Models\Traits;

use App\Models\Scopes\UserScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
        static::creating(function ($model) {
            $model->user_id = Auth::user()->id;
        });
    }
}
