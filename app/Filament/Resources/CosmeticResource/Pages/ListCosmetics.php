<?php

namespace App\Filament\Resources\CosmeticResource\Pages;

use App\Filament\Resources\CosmeticResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCosmetics extends ListRecords
{
    protected static string $resource = CosmeticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
