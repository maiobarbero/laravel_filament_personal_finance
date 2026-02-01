<?php

namespace App\Models\Traits;

use App\Models\Scopes\UserScope;

trait BelongsToUser
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new UserScope);
    }
}
