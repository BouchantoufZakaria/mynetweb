<?php

namespace App\Filament\Resources\UserDrawResource\Pages;

use App\Filament\Resources\UserDrawResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserDraw extends EditRecord
{
    protected static string $resource = UserDrawResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
