<?php

namespace App\Filament\Resources\UserSessionResource\Pages;

use App\Filament\Resources\UserSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserSessions extends ListRecords
{
    protected static string $resource = UserSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
