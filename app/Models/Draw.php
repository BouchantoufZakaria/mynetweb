<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Draw extends Model
{
    protected $fillable = [
        'date',
        'total_amount',
        'status',
    ];

    public function userDraws() : HasMany {
       return  $this->hasMany(UserDraw::class);
    }

}
