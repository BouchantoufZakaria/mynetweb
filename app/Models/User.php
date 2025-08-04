<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */


    // Schema::create('users', function (Blueprint $table) {
    //            $table->id();
    //            $table->uuid()->unique();
    //            $table->string('username');
    //            $table->text('fcm_token')->nullable();
    //            $table->string('phone_number');
    //            $table->foreignId('last_win_draw_id')
    //                ->constrained('draws')
    //            ->nullOnDelete();
    //            $table->string('access_token')->unique();
    //            $table->timestamps();
    //        });

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
