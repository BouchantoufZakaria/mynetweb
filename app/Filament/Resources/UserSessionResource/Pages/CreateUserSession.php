<?php

namespace App\Filament\Resources\UserSessionResource\Pages;

use App\Filament\Resources\UserSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserSession extends CreateRecord
{
    protected static string $resource = UserSessionResource::class;
}
