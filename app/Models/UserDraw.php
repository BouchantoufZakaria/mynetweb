<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\BelongsTo ;

class UserDraw extends Model
{

    protected $fillable = [
        'session_id',
        'user_id',
        'amount',
        'draw_id',
        'status',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function draw(): BelongsTo
    {
        return $this->belongsTo(Draw::class);
    }
}
