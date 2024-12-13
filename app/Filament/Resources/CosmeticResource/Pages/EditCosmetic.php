<?php

namespace App\Filament\Resources\CosmeticResource\Pages;

use App\Filament\Resources\CosmeticResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCosmetic extends EditRecord
{
    protected static string $resource = CosmeticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
