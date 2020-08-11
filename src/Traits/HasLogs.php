<?php

namespace ArtemiyKudin\Bonus\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasLogs
{
    /**
     * A model may have multiple logs.
     */
    public function log(): HasMany
    {
        return $this->hasMany(
            config('marketBonus.models.log'),
            config('marketBonus.column_names.users_key'),
            config('marketBonus.column_names.users_key')
        );
    }

    /**
     * A model may have one user.
     */
    public function user(): HasOne
    {
        return $this->hasOne(
            config('marketBonus.models.user'),
            config('marketBonus.column_names.users_key'),
            config('marketBonus.column_names.users_key')
        );
    }

    /**
     * A model may have one profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(
            config('marketBonus.models.profile'),
            config('marketBonus.column_names.profiles_key'),
            config('marketBonus.column_names.profiles_key')
        );
    }
}
