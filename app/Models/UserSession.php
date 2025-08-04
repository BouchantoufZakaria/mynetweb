<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{

    protected $fillable = [
        'id',
        'user_id',
        'login_at',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
