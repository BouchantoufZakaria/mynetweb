<?php

namespace App\Filament\Resources\UserSessionResource\Pages;

use App\Filament\Resources\UserSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserSession extends EditRecord
{
    protected static string $resource = UserSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
