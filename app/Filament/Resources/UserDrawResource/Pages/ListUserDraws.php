<?php

namespace App\Filament\Resources\UserDrawResource\Pages;

use App\Filament\Resources\UserDrawResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserDraws extends ListRecords
{
    protected static string $resource = UserDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
