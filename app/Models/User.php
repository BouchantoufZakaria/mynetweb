<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    use HasApiTokens ;
    protected $fillable = [
        'uuid',
        'username',
        'access_token',
        'fcm_token',
        'phone_number',
        'last_win_draw_id' ,
        'phone_number'
    ];



    public function sessions(): HasMany
    {
        return $this->hasMany(UserSession::class);
    }


    public function winDraws() : HasMany | null  {
        return  $this->hasMany(UserDraw::class) ;
    }

    public function lastWinDraw() : BelongsTo | null
    {
        return $this->belongsTo(UserDraw::class, 'last_win_draw_id', 'id');
    }



}
