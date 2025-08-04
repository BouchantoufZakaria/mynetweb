<?php

use Illuminate\Support\Facades\Route;


Route::post("/v1/game/auth/login"  , 'App\Http\Controllers\GameController@login')
    ->name('api.v1.game.auth');

Route::post("/v1/game/user" , 'App\Http\Controllers\GameController@updateUserInformation')
    ->middleware([\App\Http\Middleware\EnsureTokenIsValid::class]) ;

Route::get("/v1/game/user" , 'App\Http\Controllers\GameController@getUserInformation')
    ->middleware([\App\Http\Middleware\EnsureTokenIsValid::class]) ;

Route::post("/v1/game/user/session" , 'App\Http\Controllers\GameController@createUserSession')
    ->middleware([\App\Http\Middleware\EnsureTokenIsValid::class]) ;

Route::get("/v1/game/winners" , 'App\Http\Controllers\GameController@getLastWinnersDraws')
    ->middleware([\App\Http\Middleware\EnsureTokenIsValid::class]) ;


