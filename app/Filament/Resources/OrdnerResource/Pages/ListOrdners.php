<?php

namespace App\Filament\Resources\OrdnerResource\Pages;

use App\Filament\Resources\OrdnerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrdners extends ListRecords
{
    protected static string $resource = OrdnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
